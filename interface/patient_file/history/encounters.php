<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/pnotes.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/lists.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/sql-ledger.inc");
require_once("$srcdir/invoice_summary.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("../../../custom/code_types.inc.php");

 $accounting_enabled = $GLOBALS['oer_config']['ws_accounting']['enabled'];

 //maximum number of encounter entries to display on this page:
 $N = 12;

 // Get relevant ACL info.
 $auth_notes_a  = acl_check('encounters', 'notes_a');
 $auth_notes    = acl_check('encounters', 'notes');
 $auth_coding_a = acl_check('encounters', 'coding_a');
 $auth_coding   = acl_check('encounters', 'coding');
 $auth_relaxed  = acl_check('encounters', 'relaxed');
 $auth_med      = acl_check('patients'  , 'med');
 $auth_demo     = acl_check('patients'  , 'demo');

 $tmp = getPatientData($pid, "squad");
 if ($tmp['squad'] && ! acl_check('squads', $tmp['squad']))
  $auth_notes_a = $auth_notes = $auth_coding_a = $auth_coding = $auth_med = $auth_demo = $auth_relaxed = 0;

 if (!($auth_notes_a || $auth_notes || $auth_coding_a || $auth_coding || $auth_med || $auth_relaxed)) {
  echo "<body>\n<html>\n";
  echo "<p>(".xl('Encounters not authorized').")</p>\n";
  echo "</body>\n</html>\n";
  exit();
 }

// Perhaps the view choice should be saved as a session variable.
//
$billing_view = false;
if (isset($_GET['billing'])) {
  $billing_view = empty($_GET['billing']) ? 0 : 1;
}
else if (empty($GLOBALS['gbl_encounters_default_view'])) {
  $tmp = sqlQuery("select authorized from users " .
    "where id = '" . $_SESSION['authUserID'] . "'");
  $billing_view = ($tmp['authorized']) ? 0 : 1;
}
else if ($GLOBALS['gbl_encounters_default_view'] == 2) {
  $billing_view = true;
}

// This is called to generate a line of output for a patient document.
//
function showDocument(&$drow) {
  global $ISSUE_TYPES, $auth_med, $aTaxNames;

  $docdate = $drow['docdate'];

  echo "<tr class='text docrow' id='".$drow['id']."' title='". xl('View document') . "'>\n";

  // show date
  echo "<td>" . oeFormatShortDate($docdate) . "</td>\n";

  // show associated issue, if any
  echo "<td>";
  if ($auth_med) {
    $irow = sqlQuery("SELECT type, title, begdate " .
      "FROM lists WHERE " .
      "id = '" . $drow['list_id'] . "' " .
      "LIMIT 1");
    if ($irow) {
      $tcode = $irow['type'];
      if ($ISSUE_TYPES[$tcode]) $tcode = $ISSUE_TYPES[$tcode][2];
      echo "$tcode: " . $irow['title'];
    }
  } else {
    echo "(" . xl('No access') . ")";
  }
  echo "</td>\n";

  // show document name and category
  echo "<td colspan='3'>".
    xl('Document') . ": " .
    basename($drow['url']) . ' (' . xl_document_category($drow['name']) . ')' .
    "</td>\n";

  // skip billing and insurance columns
  if (!$GLOBALS['athletic_team']) {
    echo "<td colspan='" . (5 + count($aTaxNames)) . "'>&nbsp;</td>\n";
  }

  echo "</tr>\n";
}

// Initialize the $aItems entry for this line item if it does not yet exist.
function ensureItems($invno, $codekey) {
  global $aItems, $aTaxNames;
  if (!isset($aItems[$invno][$codekey])) {
    // Charges, Adjustments, Payments
    $aItems[$invno][$codekey] = array();
    $aItems[$invno][$codekey]['fee'] = 0;
    $aItems[$invno][$codekey]['adj'] = 0;
    $aItems[$invno][$codekey]['pay'] = 0;
    // Then a slot for each tax type.
    $aItems[$invno][$codekey]['tax'] = array();
    for ($i = 0; $i < count($aTaxNames); ++$i) {
      $aItems[$invno][$codekey]['tax'][$i] = 0;
    }
  }
}

// Get taxes matching this line item and store them in their proper $aItems array slots.
function getItemTaxes($patient_id, $encounter_id, $codekey, $id) {
  global $aItems, $aTaxNames;
  $invno = "$patient_id.$encounter_id";
  $total = 0;
  $taxres = sqlStatement("SELECT code, fee FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter_id' AND " .
    "code_type = 'TAX' AND activity = 1 AND ndc_info = '$id' " .
    "ORDER BY id");
  while ($taxrow = sqlFetchArray($taxres)) {
    $i = 0;
    $matchcount = 0;
    foreach ($aTaxNames as $tmpcode => $dummy) {
      if ($tmpcode == $taxrow['code']) {
        ++$matchcount;
        $aItems[$invno][$codekey]['tax'][$i] += $taxrow['fee'];
      }
      if ($matchcount != 1) {
        // TBD: This is an error.
        echo "ERROR: invno = '$invno' codekey = '$codekey' matchcount = '$matchcount'\n";
      }
      ++$i;
    }
    $total += $taxrow['fee'];
  }
  return $total;
}

// For a given encounter, this gets all charges and taxes and allocates payments
// and adjustments among them, if that has not already been done.
// Any invoice-level adjustments and payments are allocated among the line
// items in proportion to their line-level remaining balances.
//
// This was lifted from sales_by_item.php. Should make it a library function.
//
function ensureLineAmounts($patient_id, $encounter_id, $def_provider_id=0, $set_overpayments=false) {
  global $aItems, $aTaxNames, $overpayments;

  $invno = "$patient_id.$encounter_id";
  if (isset($aItems[$invno])) return $invno;

  $adjusts  = 0; // sum of invoice level adjustments
  $payments = 0; // sum of invoice level payments
  $denom    = 0; // sum of adjusted line item charges
  $aItems[$invno] = array();

  // From the billing table get charges, copays and taxes that are not specific
  // to line items. Then plug the service taxes into their line items.
  $tres = sqlStatement("SELECT b.code_type, b.code, b.code_text, b.fee, b.id, " .
    "u.id AS uid, u.lname, u.fname, u.username " .
    "FROM billing AS b " .
    "LEFT JOIN users AS u ON u.id = IF(b.provider_id, b.provider_id, '$def_provider_id') " .
    "WHERE " .
    "b.pid = '$patient_id' AND b.encounter = '$encounter_id' AND b.activity = 1 AND " .
    // "b.fee != 0 AND (b.code_type != 'TAX' OR b.ndc_info = '') " .
    "(b.code_type != 'TAX' OR b.ndc_info = '') " .
    "ORDER BY u.lname, u.fname, u.id, b.code_type, b.code");
  while ($trow = sqlFetchArray($tres)) {
    if ($trow['code_type'] == 'COPAY') {
      $payments -= $trow['fee'];
    }
    else {
      $codekey = $trow['code_type'] . ':' . $trow['code'];
      ensureItems($invno, $codekey);
      $denom += $trow['fee'];
      if ($trow['code_type'] == 'TAX') {
        // If this is a tax, put it in its correct column.
        $i = 0;
        foreach ($aTaxNames as $tmpcode => $dummy) {
          if ($tmpcode == $trow['code']) {
            $aItems[$invno][$codekey]['tax'][$i] += $trow['fee'];
            $trow['fee'] = 0;
          }
          ++$i;
        }
      }
      $aItems[$invno][$codekey]['fee'] += $trow['fee'];
      $denom += getItemTaxes($patient_id, $encounter_id, $codekey, 'S:' . $trow['id']);
      $aItems[$invno][$codekey]['dsc']      = $trow['code_text'];
      $aItems[$invno][$codekey]['uid']      = isset($trow['uid']     ) ? $trow['uid']      : '';
      $aItems[$invno][$codekey]['username'] = isset($trow['username']) ? $trow['username'] : '';
      $aItems[$invno][$codekey]['lname']    = isset($trow['lname']   ) ? $trow['lname']    : '';
      $aItems[$invno][$codekey]['fname']    = isset($trow['fname']   ) ? $trow['fname']    : '';
    }
  }

  // Get charges from drug_sales table and associated taxes.
  $tres = sqlStatement("SELECT s.drug_id, s.fee, s.sale_id " .
    "FROM drug_sales AS s WHERE " .
    // "s.pid = '$patient_id' AND s.encounter = '$encounter_id' AND s.fee != 0");
    "s.pid = '$patient_id' AND s.encounter = '$encounter_id'");
  while ($trow = sqlFetchArray($tres)) {
    $codekey = 'PROD:' . $trow['drug_id'];
    ensureItems($invno, $codekey);
    $aItems[$invno][$codekey]['fee'] += $trow['fee'];
    $denom += $trow['fee'];
    $denom += getItemTaxes($patient_id, $encounter_id, $codekey, 'P:' . $trow['sale_id']);
  }

  // Get adjustments and other payments from ar_activity table.
  $tres = sqlStatement("SELECT " .
    "a.code_type, a.code, a.adj_amount, a.pay_amount " .
    "FROM ar_activity AS a WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter_id'");
  while ($trow = sqlFetchArray($tres)) {
    $codekey = $trow['code_type'] . ':' . $trow['code'];
    if (isset($aItems[$invno][$codekey])) {
      $aItems[$invno][$codekey]['adj'] += $trow['adj_amount'];
      $aItems[$invno][$codekey]['pay'] += $trow['pay_amount'];
      $denom -= $trow['adj_amount'];
      $denom -= $trow['pay_amount'];
    }
    else {
      $adjusts  += $trow['adj_amount'];
      $payments += $trow['pay_amount'];
    }
  }

  // Allocate all unmatched payments and adjustments among the line items.
  $adjrem = $adjusts;  // remaining unallocated adjustments
  $payrem = $payments; // remaining unallocated payments
  $nlines = count($aItems[$invno]);
  foreach ($aItems[$invno] AS $codekey => $dummy) {
    if (--$nlines > 0) {
      // Avoid dividing by zero!
      if ($denom) {
        $bal = $aItems[$invno][$codekey]['fee'] - $aItems[$invno][$codekey]['adj'] - $aItems[$invno][$codekey]['pay'];
        for ($i = 0; $i < count($aTaxNames); ++$i) $bal += $aItems[$invno][$codekey]['tax'][$i];
        $factor = $bal / $denom;
        $tmp = sprintf('%01.2f', $adjusts * $factor);
        $aItems[$invno][$codekey]['adj'] += $tmp;
        $adjrem -= $tmp;
        $tmp = sprintf('%01.2f', $payments * $factor);
        $aItems[$invno][$codekey]['pay'] += $tmp;
        $payrem -= $tmp;
        // echo "<!-- invno = '$invno' codekey = '$codekey' denom = '$denom' bal='$bal' payments='$payments' tmp = '$tmp' -->\n"; // debugging
      }
    }
    else {
      // Last line gets what's left to avoid rounding errors.
      $aItems[$invno][$codekey]['adj'] += $adjrem;
      $aItems[$invno][$codekey]['pay'] += $payrem;
      // echo "<!-- invno = '$invno' codekey = '$codekey' payrem = '$payrem' -->\n"; // debugging
    }
  }

  if ($set_overpayments) {
    // For each line item having (payment > charge + tax - adjustment), move the
    // overpayment amount to a global variable $overpayments.
    foreach ($aItems[$invno] AS $codekey => $dummy) {
      $diff = $aItems[$invno][$codekey]['pay'] + $aItems[$invno][$codekey]['adj'] - $aItems[$invno][$codekey]['fee'];
      for ($i = 0; $i < count($aTaxNames); ++$i) $diff -= $aItems[$invno][$codekey]['tax'][$i];
      $diff = sprintf('%01.2f', $diff);
      if ($diff > 0.00) {
        $overpayments += $diff;
        $aItems[$invno][$codekey]['pay'] -= $diff;
      }
    }
  }

  return $invno;
}

function getTaxNames($pid) {
  global $aTaxNames;
  // Get the tax types applicable to this patient.
  $aTaxNames = array();
  $tnres = sqlStatement("SELECT DISTINCT b.code, b.code_text " .
    "FROM billing AS b WHERE " .
    "b.code_type = 'TAX' AND b.activity = '1' AND b.pid = '$pid' " .
    "ORDER BY b.code, b.code_text");
  while ($tnrow = sqlFetchArray($tnres)) {
    $aTaxNames[$tnrow['code']] = $tnrow['code_text'];
  }
}

getTaxNames($pid);
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">

<style type="text/css">
.openvisit {background-color:#ffffcc;}
#patient_pastenc th.right {text-align:right; padding-right:5pt;}
#patient_pastenc td.right {text-align:right; padding-right:5pt;}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/ajtooltip.js"></script>

<script language="JavaScript">

//function toencounter(enc, datestr) {
function toencounter(rawdata) {
    var parts = rawdata.split("~");
    var enc = parts[0];
    var datestr = parts[1];

    top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
    parent.left_nav.setEncounter(datestr, enc, window.name);
    parent.left_nav.setRadio(window.name, 'enc');
    parent.left_nav.loadFrame('enc2', window.name, 'patient_file/encounter/encounter_top.php?set_encounter=' + enc);
<?php } else { ?>
    top.Title.location.href = '../encounter/encounter_title.php?set_encounter='   + enc;
    top.Main.location.href  = '../encounter/patient_encounter.php?set_encounter=' + enc;
<?php } ?>
}

function todocument(docid) {
  h = '<?php echo $GLOBALS['webroot'] ?>/controller.php?document&view&patient_id=<?php echo $pid ?>&doc_id=' + docid;
  top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
  parent.left_nav.setRadio(window.name, 'doc');
  location.href = h;
<?php } else { ?>
  top.Main.location.href = h;
<?php } ?>
}

 // Helper function to set the contents of a div.
function setDivContent(id, content) {
    $("#"+id).html(content);
}

 // Called when clicking on a billing note.
function editNote(feid) {
  top.restoreSession(); // this is probably not needed
  var c = "<iframe src='edit_billnote.php?feid=" + feid +
    "' style='width:100%;height:88pt;'></iframe>";
  setDivContent('note_' + feid, c);
}

 // Called when the billing note editor closes.
 function closeNote(feid, fenote) {
    var c = "<div id='"+ feid +"' title='<?php xl('Click to edit','e'); ?>' class='text billing_note_text'>" +
            fenote + "</div>";
    setDivContent('note_' + feid, c);
 }

 // Mouseover handler for encounter form names. Brings up a custom tooltip
 // to display the form's contents.
 function efmouseover(elem, ptid, encid, formname, formid) {
  ttMouseOver(elem, "encounters_ajax.php?ptid=" + ptid + "&encid=" + encid +
   "&formname=" + formname + "&formid=" + formid);
 }

</script>

</head>

<body class="body_bottom">
<div id="patient_pastenc"> <!-- large outer DIV -->

<?php if ($GLOBALS['concurrent_layout']) { ?>
<!-- <a href='encounters_full.php'> -->
<?php } else { ?>
<!-- <a href='encounters_full.php' target='Main'> -->
<?php } ?>
<font class='title'><?php xl('Past Encounters and Documents','e'); ?></font>
&nbsp;&nbsp;

<?php if ($billing_view) { ?>
<a href='encounters.php?billing=0' onclick='top.restoreSession()' style='font-size:8pt'>(<?php xl('To Clinical View','e'); ?>)</a>
<?php } else { ?>
<a href='encounters.php?billing=1' onclick='top.restoreSession()' style='font-size:8pt'>(<?php xl('To Billing View','e'); ?>)</a>
<?php } ?>

<br>

<table>
 <tr class='text'>
  <th><?php xl('Date','e'); ?></th>
  <th><?php xl('Open','e'); ?></th>

<?php if ($billing_view) { ?>
  <th class='billing_note'><?php xl('Billing Note','e'); ?></th>
<?php } else { ?>
  <th><?php xl('Issue','e');       ?></th>
  <th><?php xl('Reason/Form','e'); ?></th>
  <th><?php xl('Provider','e');    ?></th>
<?php } ?>

<?php if ($billing_view && $accounting_enabled) { ?>
  <th><?php xl('Code','e'); ?></th>
  <th class='right'><?php xl('Charge','e'); ?></th>
  <th class='right'><?php xl('Adj','e'); ?></th>
<?php
  foreach ($aTaxNames as $taxname) {
    echo "  <th class='right'>" . htmlspecialchars($taxname) . "</th>\n";
  }
?>
  <th class='right'><?php xl('Paid','e'); ?></th>
  <th class='right'><?php xl('Bal','e'); ?></th>
<?php } else { ?>
  <th colspan='<?php echo 5 + count($aTaxNames); ?>'><?php echo
    ($GLOBALS['phone_country_code'] == '1') ? xl('Billing') : xl('Coding') ?></th>
<?php } ?>

<?php if (!$GLOBALS['athletic_team'] && !$GLOBALS['ippf_specific']) { ?>
  <th>&nbsp;<?php echo ($GLOBALS['weight_loss_clinic']) ? xl('Payment') : xl('Insurance'); ?></th>
<?php } ?>

 </tr>

<?php
$drow = false;
if (! $billing_view) {
    // Query the documents for this patient.
    $dres = sqlStatement("SELECT d.id, d.type, d.url, d.docdate, d.list_id, c.name " .
                    "FROM documents AS d, categories_to_documents AS cd, categories AS c WHERE " .
                    "d.foreign_id = '$pid' AND cd.document_id = d.id AND c.id = cd.category_id " .
                    "ORDER BY d.docdate DESC, d.id DESC");
    $drow = sqlFetchArray($dres);
}

$count = 0;

// For each of the patient's encounters...
if ($result = getEncounters($pid)) {

    foreach ($result as $iter ) {

        // $href = "javascript:window.toencounter(" . $iter['encounter'] . ")";
        $reason_string = "";
        $auth_sensitivity = true;

        $raw_encounter_date = '';

        // Get some encounter attributes.
        if ($result4 = sqlQuery("SELECT fe.*, u.fname, u.mname, u.lname " .
          "FROM form_encounter AS fe " .
          "LEFT JOIN users AS u ON u.id = fe.provider_id " .
          "WHERE fe.encounter = '" . $iter["encounter"] .
          "' AND fe.pid = '$pid'"))
        {
            $raw_encounter_date = date("Y-m-d", strtotime($result4{"date"}));
            $encounter_date = date("D F jS", strtotime($result4{"date"}));

            // if ($auth_notes_a || ($auth_notes && $iter['user'] == $_SESSION['authUser']))
            $reason_string .= $result4{"reason"} . "<br>\n";
            // else
            //   $reason_string = "(No access)";

            if ($result4['sensitivity']) {
                $auth_sensitivity = acl_check('sensitivities', $result4['sensitivity']);
                if (!$auth_sensitivity) {
                    $reason_string = "(No access)";
                }
            }
        }

        // Get ID of user who created the encounter.
        $erow = sqlQuery("SELECT user FROM forms WHERE encounter = '" .
        $iter['encounter'] . "' AND formdir = 'newpatient' LIMIT 1");

        // This generates document lines as appropriate for the date order.
        while ($drow && $raw_encounter_date && $drow['docdate'] > $raw_encounter_date) {
            showDocument($drow);
            $drow = sqlFetchArray($dres);
        }

        // We will call a visit "open" if it is not billed.
        // Dummy encounters from conversion of contraception start dates are considered billed.
        $billed = $result4['reason'] == 'PreOpenEMR Data' || isEncounterBilled($pid, $iter['encounter']);

        // Fetch all forms for this encounter, if the user is authorized to see
        // this encounter's notes and this is the clinical view.
        $encarr = array();
        $encounter_rows = 1;
        if (!$billing_view && $auth_sensitivity &&
            ($auth_notes_a || ($auth_notes && $iter['user'] == $_SESSION['authUser'])))
        {
            $encarr = getFormByEncounter($pid, $iter['encounter'], "formdir, user, form_name, form_id, deleted");
            $encounter_rows = count($encarr);
        }

        $rawdata = $iter['encounter'] . "~" . oeFormatShortDate($raw_encounter_date);
        echo "<tr class='encrow text";
        // This color should be moved to the stylesheet when porting to current code.
        if (!$billed) echo " openvisit";
        echo "' id='".$rawdata."'";
        echo ">\n";

        // show encounter date
        echo "<td valign='top' title='" .
          xl('View encounter','','',' ') .
          "$pid.{$iter['encounter']}'>" .
          oeFormatShortDate($raw_encounter_date) . "</td>\n";

        // show if unbilled
        echo "<td valign='top'>" . ($billed ? xl('No') : xl('Yes')) . "</td>\n";

        if ($billing_view) {

            // Show billing note that you can click on to edit.
            $feid = $result4['id'] ? $result4['id'] : 0; // form_encounter id
            echo "<td valign='top'>";
            echo "<div id='note_$feid'>";
            //echo "<div onclick='editNote($feid)' title='Click to edit' class='text billing_note_text'>";
            echo "<div id='$feid' title='". xl('Click to edit') . "' class='text billing_note_text'>";
            echo $result4['billing_note'] ? nl2br($result4['billing_note']) : xl('Add','','[',']');
            echo "</div>";
            echo "</div>";
            echo "</td>\n";

        }
        else { //clinical view

            // show issues for this encounter
            echo "<td>";
            if ($auth_med && $auth_sensitivity) {
                $ires = sqlStatement("SELECT lists.type, lists.title, lists.begdate " .
                                    "FROM issue_encounter, lists WHERE " .
                                    "issue_encounter.pid = '$pid' AND " .
                                    "issue_encounter.encounter = '" . $iter['encounter'] . "' AND " .
                                    "lists.id = issue_encounter.list_id " .
                                    "ORDER BY lists.type, lists.begdate");
                for ($i = 0; $irow = sqlFetchArray($ires); ++$i) {
                    if ($i > 0) echo "<br>";
                    $tcode = $irow['type'];
                    if ($ISSUE_TYPES[$tcode]) $tcode = $ISSUE_TYPES[$tcode][2];
                        echo "$tcode: " . $irow['title'];
                }
            } 
            else {
                echo "(" . xl('No access') . ")";
            }
            echo "</td>\n";

            // show encounter reason/title
            echo "<td>".$reason_string;
            echo "<div style='padding-left:10px;'>";

            // Now show a line for each encounter form, if the user is authorized to
            // see this encounter's notes.

            foreach ($encarr as $enc) {
                if ($enc['formdir'] == 'newpatient') continue;
            
                // skip forms whose 'deleted' flag is set to 1 --JRM--
                if ($enc['deleted'] == 1) continue;
    
                // Skip forms that we are not authorized to see. --JRM--
                // pardon the wonky logic
                $formdir = $enc['formdir'];
                if (($auth_notes_a) ||
                    ($auth_notes && $enc['user'] == $_SESSION['authUser']) ||
                    ($auth_relaxed && ($formdir == 'sports_fitness' || $formdir == 'podiatry'))) ;
                else continue;

                echo "<div " .
                  "onmouseover='efmouseover(this,$pid," . $iter['encounter'] . ",\"" .
                  $enc['formdir'] . "\"," . $enc['form_id'] . ")' " .
                  "onmouseout='ttMouseOut()'>";
                echo xl_form_title($enc['form_name']);
                echo "</div><!-- <br> -->";

            } // end encounter Forms loop
    
            echo "</div>";
            echo "</td>\n";

        } // end clinical view

        //this is where we print out the text of the billing that occurred on this encounter
        $thisauth = $auth_coding_a;
        if (!$thisauth && $auth_coding) {
            if ($erow['user'] == $_SESSION['authUser'])
                $thisauth = $auth_coding;
        }

        $hprovs = ''; // will hold html for providers
        $hcodes = ''; // will hold html for billing details
        $def_provider_id = 0 + $result4['provider_id'];
        $last_provider_id = -1;
        $def_provider_used = false;

        $arid = 0;
        if ($thisauth && $auth_sensitivity) {
            $binfo = array();
            for ($i = 0; $i < 5 + count($aTaxNames); ++$i) {
              $binfo[$i] = '';
            }
            $aItems = array();
            ensureLineAmounts($pid, $iter['encounter'], $def_provider_id);
 
            $invno = "$pid." . $iter['encounter'];

            foreach ($aItems[$invno] AS $codekey => $arr) {
              $title = addslashes($arr['dsc']);

              // Append to provider name HTML.
              $provider_id = empty($arr['uid']) ? 0 : 0 + $arr['uid'];
              if ($provider_id != $last_provider_id) {
                $last_provider_id = $provider_id;
                $provname = '(' . xl('Unknown') . ')';
                if ($provider_id) {
                  if (empty($arr['lname']) && empty($arr['fname'])) {
                    $provname = '(' . xl('No name') . ')';
                  }
                  else {
                    $provname = $arr['lname'];
                    if ($arr['fname']) $provname .= ', ' . $arr['fname'];
                  }
                }
                if (!empty($hprovs)) $hprovs .= '<br />';
                if ($provider_id == $def_provider_id) {
                  $def_provider_used = true;
                  $hprovs .= "<b>$provname</b>";
                }
                else {
                  $hprovs .= "$provname";
                }
              }
              else {
                $hprovs .= "&nbsp;<br />";
              }

              if ($binfo[0]) $binfo[0] .= '<br>';
              $binfo[0] .= "<span title='$title'>$arlinkbeg$codekey$arlinkend</span>";

              if ($billing_view && $accounting_enabled) {
                if ($binfo[1]) {
                  for ($i = 1; $i < 5 + count($aTaxNames); ++$i) $binfo[$i] .= '<br>';
                }
                $binfo[1] .= oeFormatMoney($arr['fee']);
                $binfo[2] .= oeFormatMoney($arr['adj']);
                $tax = 0;
                $i = 0;
                for (; $i < count($aTaxNames); ++$i) {
                  $binfo[3 + $i] .= oeFormatMoney($arr['tax'][$i]);
                  $tax += $arr['tax'][$i];
                }
                $binfo[3 + $i] .= oeFormatMoney($arr['pay']);
                $binfo[4 + $i] .= oeFormatMoney($arr['fee'] - $arr['adj'] + $tax - $arr['pay']);
              }
            }

            $hcodes .= "<td class='text'>" . $binfo[0] . "</td>\n";       // billing code
            for ($i = 1; $i < 5 + count($aTaxNames); ++$i) {
              $hcodes .= "<td class='text right'>" . $binfo[$i] . "</td>\n";
            }

        } // end if authorized

        else { // access to coding is not authorized
            $hcodes .= "<td class='text' valign='top' colspan='" . (5 + count($aTaxNames)) .
              "' rowspan='$encounter_rows'>(No access)</td>\n";
        }

        // Write the providers column if this is the clinical view.
        if (!$billing_view) {
          if (!$def_provider_used) {
            // show default provider for the encounter
            $provname = '(' . xl('Unknown') . ')';
            if ($def_provider_id) {
              if (empty($result4['lname']) && empty($result4['fname'])) {
                $provname = '(' . xl('No name') . ')';
              }
              else {
                $provname = $result4['lname'];
                if ($result4['fname']) $provname .= ', ' . $result4['fname'];
              }
            }
            if (!empty($hprovs)) $hprovs .= '<br />';
            $hprovs .= "<b>$provname</b>";
          }
          echo "<td>$hprovs</td>\n";
        }

        // Write the billing info.
        echo $hcodes;

        // show insurance
        if (!$GLOBALS['athletic_team'] && !$GLOBALS['ippf_specific']) {
            $insured = oeFormatShortDate($raw_encounter_date);
            if ($auth_demo) {
                $responsible = -1;
                if ($arid) {
                    $responsible = ar_responsible_party($pid, $iter['encounter']);
                }
                $subresult5 = getInsuranceDataByDate($pid, $raw_encounter_date, "primary");
                if ($subresult5 && $subresult5{"provider_name"}) {
                    $style = $responsible == 1 ? " style='color:red'" : "";
                    $insured = "<span class='text'$style>&nbsp;" . xl('Primary') . ": " .
                    $subresult5{"provider_name"} . "</span><br>\n";
                }
                $subresult6 = getInsuranceDataByDate($pid, $raw_encounter_date, "secondary");
                if ($subresult6 && $subresult6{"provider_name"}) {
                    $style = $responsible == 2 ? " style='color:red'" : "";
                    $insured .= "<span class='text'$style>&nbsp;" . xl('Secondary') . ": " .
                    $subresult6{"provider_name"} . "</span><br>\n";
                }
                $subresult7 = getInsuranceDataByDate($pid, $raw_encounter_date, "tertiary");
                if ($subresult6 && $subresult7{"provider_name"}) {
                    $style = $responsible == 3 ? " style='color:red'" : "";
                    $insured .= "<span class='text'$style>&nbsp;" . xl('Tertiary') . ": " .
                    $subresult7{"provider_name"} . "</span><br>\n";
                }
                if ($responsible == 0) {
                    $insured .= "<span class='text' style='color:red'>&nbsp;" . xl('Patient') .
                                "</span><br>\n";
                }
            }
            else {
                $insured = " (No access)";
            }
      
            echo "<td>".$insured."</td>\n";
        }

        echo "</tr>\n";


    } // end foreach $result

} // end if

// Dump remaining document lines if count not exceeded.
while ($drow && $count <= $N) {
    showDocument($drow);
    $drow = sqlFetchArray($dres);
}
?>

</table>

</div> <!-- end 'pastenc' large outer DIV -->

<div id='tooltipdiv'
 style='position:absolute;width:400pt;border:1px solid black;padding:2px;background-color:#ffffaa;visibility:hidden;z-index:1000;font-size:9pt;'
></div>

</body>

<script language="javascript">
// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    $(".encrow").mouseover(function() { $(this).toggleClass("highlight"); });
    $(".encrow").mouseout(function() { $(this).toggleClass("highlight"); });
    $(".encrow").click(function() { toencounter(this.id); }); 
    
    $(".docrow").mouseover(function() { $(this).toggleClass("highlight"); });
    $(".docrow").mouseout(function() { $(this).toggleClass("highlight"); });
    $(".docrow").click(function() { todocument(this.id); }); 

    $(".billing_note_text").mouseover(function() { $(this).toggleClass("billing_note_text_highlight"); });
    $(".billing_note_text").mouseout(function() { $(this).toggleClass("billing_note_text_highlight"); });
    $(".billing_note_text").click(function(evt) { evt.stopPropagation(); editNote(this.id); });
});

</script>

</html>

<?php
// Copyright (C) 2012 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This is a report of invoice payments with a column per payment method.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");

$insarray = array();
$patients = array();

function bucks($amount) {
  if ($amount) echo oeFormatMoney($amount);
}

function recordPayment($encdate, $patient_id, $encounter_id,
  $rowmethod, $rowchgamount, $rowpayamount, $rowadjamount)
{
  global $insarray, $metharray, $grandchgtotal, $grandadjtotal, $patients;

  /*******************************************************************
  echo "<!-- " .
    "encdate = '$encdate' " .
    "patient_id = '$patient_id' " .
    "encounter_id = '$encounter_id' " .
    "rowmethod = '$rowmethod' " .
    "rowchgamount = '$rowchgamount' " .
    "rowpayamount = '$rowpayamount' " .
    "rowadjamount = '$rowadjamount' " .
    "-->\n"; // debugging
  *******************************************************************/

  // Extract only the first word as the payment method because any
  // following text will be some petty detail like a check number.
  $rowmethod = substr($rowmethod, 0, strcspn($rowmethod, ' /'));

  // Unexpected method is translated to Unassigned.
  if (empty($rowmethod)) $rowmethod = 'zzz';

  // If not a recognized method, create a new entry for it.
  if ($rowpayamount != 0 && !isset($metharray[$rowmethod])) {
    echo "<!-- Adding method '$rowmethod' -->\n";
    $metharray[$rowmethod] = array('title' => $rowmethod, 'paytotal' => 0);
  }

  // Get patient info here so names will be available at sort time.
  if (!isset($patients[$patient_id])) {
    $patients[$patient_id] = sqlQuery("SELECT " .
      "pubpid, DOB, fname, mname, lname " .
      "FROM patient_data WHERE pid = '$patient_id' LIMIT 1");
  }

  // If necessary create missing array hierarchy elements.
  if (!isset($insarray[$encdate]))
    $insarray[$encdate] = array();
  if (!isset($insarray[$encdate][$patient_id]))
    $insarray[$encdate][$patient_id] = array();
  if (!isset($insarray[$encdate][$patient_id][$encounter_id])) {
    // First entry is accumulator for charges.
    // Second entry is accumulator for adjustments.
    $insarray[$encdate][$patient_id][$encounter_id] = array('$' => 0, '@' => 0);
    // After that is an accumulator for each payment method.
    // foreach ($metharray as $method => $dummy) {
    //   $insarray[$encdate][$patient_id][$encounter_id][$method] = 0;
    // }
  }
  if (!isset($insarray[$encdate][$patient_id][$encounter_id][$rowmethod])) {
    $insarray[$encdate][$patient_id][$encounter_id][$rowmethod] = 0;
  }

  // Accumulate charges, payments and adjustments.
  $insarray[$encdate][$patient_id][$encounter_id][$rowmethod] += $rowpayamount;
  $insarray[$encdate][$patient_id][$encounter_id]['$']        += $rowchgamount;
  $insarray[$encdate][$patient_id][$encounter_id]['@']        += $rowadjamount;

  // Accumulate also for bottom line totals.
  if ($rowpayamount != 0) $metharray[$rowmethod]['paytotal'] += $rowpayamount;
  $grandchgtotal += $rowchgamount;
  $grandadjtotal += $rowadjamount;
}

// This is called by usort(). Compares by patient name given pids.
function ptSortCmp($a, $b) {
  global $patients;
  if ($patients[$a]['lname'] < $patients[$b]['lname']) return -1;
  if ($patients[$a]['lname'] > $patients[$b]['lname']) return  1;
  if ($patients[$a]['fname'] < $patients[$b]['fname']) return -1;
  if ($patients[$a]['fname'] > $patients[$b]['fname']) return  1;
  if ($patients[$a]['mname'] < $patients[$b]['mname']) return -1;
  if ($patients[$a]['mname'] > $patients[$b]['mname']) return  1;
  return 0;
}

if (! acl_check('acct', 'rep')) die(xl("Unauthorized access."));

$form_use_edate = $_POST['form_use_edate'];
$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date   = fixDate($_POST['form_to_date']  , date('Y-m-d'));
$form_facility  = $_POST['form_facility'];

// Build array of payment methods. Key is method ID, value is an array of title
// and payment total accumulator.  Last entry is for no method.
//
$metharray = array();
$mres = sqlStatement("SELECT option_id, title FROM list_options WHERE " .
  "list_id = 'paymethod' ORDER BY seq, title");
while ($mrow = sqlFetchArray($mres)) {
  if ($mrow['option_id'] === '') continue;
  $metharray[$mrow['option_id']] = array('title' => $mrow['title'], 'paytotal' => 0);
}
$metharray['zzz'] = array('title' => xl('Unassigned'), 'paytotal' => 0);
//
?>
<html>
<head>
<style>
td.dehead { font-size:10pt; text-align:center; }
td.detail { font-size:10pt; }
</style>
<?php if (function_exists('html_header_show')) html_header_show(); ?>
<title><?php xl('Payment Methods by Invoice','e') ?></title>
</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
<center>

<h2><?php xl('Payment Methods by Invoice','e') ?></h2>

<form method='post' action='methods_by_invoice.php'>

<table border='0' cellpadding='3'>

 <tr>
  <td>

<?php
// Build a drop-down list of facilities.
//
$query = "SELECT id, name FROM facility ORDER BY name";
$fres = sqlStatement($query);
echo "   <select name='form_facility'>\n";
echo "    <option value=''>-- " . xl('All Facilities') . " --\n";
while ($frow = sqlFetchArray($fres)) {
  $facid = $frow['id'];
  echo "    <option value='$facid'";
  if ($facid == $form_facility) echo " selected";
  echo ">" . $frow['name'] . "\n";
}
echo "   </select>\n";
?>
  </td>
  <td>
   <select name='form_use_edate'>
    <option value='0'><?php xl('Payment Date','e'); ?></option>
    <option value='1'<?php if ($form_use_edate) echo ' selected' ?>><?php xl('Invoice Date','e'); ?></option>
   </select>
   &nbsp;<?xl('From:','e')?>
   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo $form_from_date ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>
   &nbsp;<?php xl('To:','e'); ?>
   <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo $form_to_date ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>
   &nbsp;
   <input type='submit' name='form_refresh' value="<?xl('Refresh','e')?>">
   &nbsp;
   <input type='button' value='<?php xl('Print','e'); ?>' onclick='window.print()' />
  </td>
 </tr>

 <tr>
  <td height="1">
  </td>
 </tr>
<?php

if ($_POST['form_refresh']) {
  $from_date = $form_from_date;
  $to_date   = $form_to_date;

  $paymethod   = "";
  $paymethodleft = "";
  $grandchgtotal  = 0;
  $grandadjtotal  = 0;

  // Get co-pays using the encounter date as the pay date.  These will
  // always be considered patient payments.
  //
  $query = "SELECT b.fee, b.pid, b.encounter, b.code_type, b.code_text, " .
    "fe.date, fe.facility_id, fe.invoice_refno " .
    "FROM billing AS b " .
    "JOIN form_encounter AS fe ON fe.pid = b.pid AND fe.encounter = b.encounter " .
    "WHERE b.activity = 1 AND b.fee != 0 AND " .
    "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";
  // If a facility was specified.
  if ($form_facility) $query .= " AND fe.facility_id = '$form_facility'";
  $query .= " ORDER BY fe.date, b.pid, b.encounter, fe.id";
  //
  $res = sqlStatement($query);
  while ($row = sqlFetchArray($res)) {
    if ($row['code_type'] == 'COPAY') {
      $rowmethod = trim($row['code_text']);
      recordPayment(substr($row['date'], 0, 10), $row['pid'], $row['encounter'],
        $rowmethod, 0, 0 - $row['fee'], 0);
    }
    else {
      // Record a charge. Note these will only be the charges from the report period.
      recordPayment(substr($row['date'], 0, 10), $row['pid'], $row['encounter'],
        '', $row['fee'], 0, 0);
    }
  }

  // Get all other payments and adjustments and their dates, corresponding
  // payers and check reference data, and the encounter dates separately.
  //
  $query = "SELECT a.pid, a.encounter, a.post_time, a.pay_amount, " .
    "a.adj_amount, a.memo, a.session_id, a.code, a.payer_type, fe.id, fe.date, " .
    "fe.invoice_refno, s.deposit_date, s.payer_id, s.reference " .
    "FROM ar_activity AS a " .
    "JOIN form_encounter AS fe ON fe.pid = a.pid AND fe.encounter = a.encounter " .
    "JOIN forms AS f ON f.pid = a.pid AND f.encounter = a.encounter AND f.formdir = 'newpatient' " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id " .
    "WHERE ( a.pay_amount != 0 OR a.adj_amount != 0 )";
  if ($form_use_edate) {
    $query .= " AND fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";
  } else {
    $query .= " AND ( ( s.deposit_date IS NOT NULL AND " .
      "s.deposit_date >= '$from_date' AND s.deposit_date <= '$to_date' ) OR " .
      "( s.deposit_date IS NULL AND a.post_time >= '$from_date 00:00:00' AND " .
      "a.post_time <= '$to_date 23:59:59' ) )";
  }
  // If a facility was specified.
  if ($form_facility) $query .= " AND fe.facility_id = '$form_facility'";
  //
  $query .= " ORDER BY fe.date, a.pid, a.encounter, fe.id";
  //
  $res = sqlStatement($query);
  while ($row = sqlFetchArray($res)) {
    $encdate = substr($row['date'], 0, 10);
    // Compute payment method.
    if (empty($row['session_id'])) {
      $rowmethod = trim($row['memo']);
    } else {
      $rowmethod = trim($row['reference']);
    }
    recordPayment($encdate, $row['pid'], $row['encounter'],
      $rowmethod, 0, $row['pay_amount'], $row['adj_amount']);
  }

  // echo "<!-- insarray:\n"; print_r($insarray); echo " -->\n"; // debugging
?>

</table>

<table border='0' cellpadding='1' cellspacing='2' width='98%'>

 <tr bgcolor="#dddddd" style="font-weight:bold">
  <td class="dehead">
   <?php xl('Svc Date','e') ?>
  </td>
  <td class="dehead">
   <?php xl('Patient','e') ?>
  </td>
  <td class="dehead">
   <?php xl('ID','e') ?>
  </td>
  <td class="dehead">
   <?php xl('DOB','e') ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Charges','e') ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Adjustments','e') ?>
  </td>
<?php
ksort($metharray);
foreach ($metharray as $key => $value) {
  echo "  <td class='dehead' align='right'>\n";
  echo htmlspecialchars($value['title']);
  echo "  </td>\n";
}
?>
 </tr>

<?php
  ksort($insarray);                 // sort by encounter date
  $encount = 0;
  foreach ($insarray as $encdate => $datearray) {
    $disp_dos  = oeFormatShortDate($encdate);
    uksort($datearray, 'ptSortCmp'); // sort by patient name
    foreach ($datearray as $ptid => $ptarray) {
      $disp_id   = $patients[$ptid]['pubpid'];
      $disp_dob  = oeFormatShortDate(substr($patients[$ptid]['DOB'], 0, 10));
      $disp_name = $patients[$ptid]['lname'] . ", " .
        $patients[$ptid]['fname'] . " " . $patients[$ptid]['mname'];      

      ksort($ptarray);              // sort by encounter ID

      foreach ($ptarray as $encid => $encarray) {
        ++$encount;
        $bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");
        echo " <tr bgcolor='$bgcolor'>\n";
?>
  <td class="dehead">
   <?php echo $disp_dos; ?>
  </td>
  <td class="detail">
   <?php echo $disp_name; ?>
  </td>
  <td class="detail">
   <?php echo $disp_id; ?>
  </td>
  <td class="detail">
   <?php echo $disp_dob; ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($insarray[$encdate][$ptid][$encid]['$']); ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($insarray[$encdate][$ptid][$encid]['@']); ?>
  </td>
<?php
        foreach ($metharray as $meth => $dummy) {
          echo "  <td class='detail' align='right'>\n";
          bucks($insarray[$encdate][$ptid][$encid][$meth]);
          echo "  </td>\n";
        }
        echo " </tr>\n";
        $disp_dos  = '&nbsp;';
        $disp_name = '&nbsp;';
        $disp_id   = '&nbsp;';
        $disp_dob  = '&nbsp;';
      }
    }
  }
?>
 <tr bgcolor="#dddddd" style="font-weight:bold">
  <td class="detail" colspan="4">
   <?php xl('Grand Totals','e'); ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($grandchgtotal); ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($grandadjtotal); ?>
  </td>
<?php
  foreach ($metharray as $meth => $value) {
    echo "  <td class='detail' align='right'>\n";
    bucks($value['paytotal']);
    echo "  </td>\n";
  }
?>
 </tr>

<?php
} // end form refresh
?>

</table>
</form>
</center>
</body>

<!-- stuff for the popup calendar -->
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script language="Javascript">
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});
</script>

</html>

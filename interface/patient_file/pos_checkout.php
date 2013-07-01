<?php
// Copyright (C) 2006-2013 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This module supports a popup window to handle patient checkout
// as a point-of-sale transaction.  Support for in-house drug sales
// is included.

// Important notes about system design:
//
// (1) Drug sales may or may not be associated with an encounter;
//     they are if they are paid for concurrently with an encounter, or
//     if they are "product" (non-prescription) sales via the Fee Sheet.
//     UPDATE: ENCOUNTER IS NOW ALWAYS REQUIRED.
// (2) Drug sales without an encounter will have 20YYMMDD, possibly
//     with a suffix, as the encounter-number portion of their invoice
//     number.
//     UPDATE: ENCOUNTER IS NOW ALWAYS REQUIRED.
// (3) Payments are saved as AR only, don't mess with the billing table.
//     See library/classes/WSClaim.class.php for posting code.
// (4) On checkout, the billing and drug_sales table entries are marked
//     as billed and so become unavailable for further billing.
// (5) Receipt printing must be a separate operation from payment,
//     and repeatable.

require_once("../globals.php");
require_once("$srcdir/acl.inc");
require_once("$srcdir/patient.inc");
require_once("$srcdir/billing.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/date_functions.php");
require_once("$srcdir/formdata.inc.php");
require_once("../../custom/code_types.inc.php");
require_once("$srcdir/calendar_events.inc.php");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/sl_eob.inc.php");

$currdecimals = $GLOBALS['currency_decimals'];

if ($GLOBALS['oer_config']['ws_accounting']['enabled'] !== 2)
  die("SQL-Ledger not supported!");

// Details default to yes now.
$details = (!isset($_GET['details']) || !empty($_GET['details'])) ? 1 : 0;

$patient_id = empty($_GET['ptid']) ? $pid : 0 + $_GET['ptid'];

// This flag comes from the Fee Sheet form and perhaps later others.
$rapid_data_entry = empty($_GET['rde']) ? 0 : 1;

// Get the patient's name and chart number.
$patdata = getPatientData($patient_id, 'fname,mname,lname,pubpid,street,city,state,postal_code');

// Adjustments from the ar_activity table.
$aAdjusts = array();

// Get a list item's title, translated if appropriate.
//
function getListTitle($list, $option) {
  $row = sqlQuery("SELECT title FROM list_options WHERE " .
    "list_id = '$list' AND option_id = '$option'");
  if (empty($row['title'])) return $option;
  return xl_list_label($row['title']);
}

// Get the "next invoice reference number" from this user's pool.
//
function getInvoiceRefNumber() {
  $trow = sqlQuery("SELECT lo.notes " .
    "FROM users AS u, list_options AS lo " .
    "WHERE u.username = '" . $_SESSION['authUser'] . "' AND " .
    "lo.list_id = 'irnpool' AND lo.option_id = u.irnpool LIMIT 1");
  return empty($trow['notes']) ? '' : $trow['notes'];
}

// Increment the "next invoice reference number" of this user's pool.
// This identifies the "digits" portion of that number and adds 1 to it.
// If it contains more than one string of digits, the last is used.
//
function updateInvoiceRefNumber() {
  $irnumber = getInvoiceRefNumber();
  // Here "?" specifies a minimal match, to get the most digits possible:
  if (preg_match('/^(.*?)(\d+)(\D*)$/', $irnumber, $matches)) {
    $newdigs = sprintf('%0' . strlen($matches[2]) . 'd', $matches[2] + 1);
    $newnumber = add_escape_custom($matches[1] . $newdigs . $matches[3]);
    sqlStatement("UPDATE users AS u, list_options AS lo " .
      "SET lo.notes = '$newnumber' WHERE " .
      "u.username = '" . $_SESSION['authUser'] . "' AND " .
      "lo.list_id = 'irnpool' AND lo.option_id = u.irnpool");
  }
  return $irnumber;
}

// Output HTML for a receipt line item.
//
// function receiptDetailLine($svcdate, $description, $amount, $quantity) {
function receiptDetailLine($code_type, $code, $description, $quantity, $charge, &$aTotals='') {
  global $details, $aAdjusts;

  $adjust = 0;

  // If an adjustment, get it into the right column.
  if ($code_type === '') {
    $adjust = 0 - $charge;
    $charge = 0;
  }
  else {
    if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
      // Total and clear adjustments in $aAdjusts matching this line item.
      for ($i = 0; $i < count($aAdjusts); ++$i) {
        if ($aAdjusts[$i]['code_type'] == $code_type && $aAdjusts[$i]['code'] == $code && $aAdjusts[$i]['adj_amount'] != 0) {
          $adjust += $aAdjusts[$i]['adj_amount'];
          $aAdjusts[$i]['adj_amount'] = 0;
        }
      }
    }
  }

  $charge = sprintf('%01.2f', $charge);
  $total  = sprintf('%01.2f', $charge - $adjust);
  if (empty($quantity)) $quantity = 1;
  $price = sprintf('%01.4f', $charge / $quantity);
  $tmp = sprintf('%01.2f', $price);
  if ($price == $tmp) $price = $tmp;

  if (is_array($aTotals)) {
    $aTotals[0] += $quantity;
    $aTotals[1] += $price;
    $aTotals[2] += $charge;
    $aTotals[3] += $adjust;
    $aTotals[4] += $total;
  }

  if (!$details) return;

  echo " <tr>\n";
  echo "  <td>$code</td>\n";
  echo "  <td>$description</td>\n";
  echo "  <td align='center'>$quantity</td>\n";
  echo "  <td align='right'>" . oeFormatMoney($price,false,true) . "</td>\n";
  if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
    echo "  <td align='right'>" . oeFormatMoney($charge,false,true) . "</td>\n";
    echo "  <td align='right'>" . oeFormatMoney($adjust,false,true) . "</td>\n";
  }
  echo "  <td align='right'>" . oeFormatMoney($total) . "</td>\n";
  echo " </tr>\n";
}

// Output HTML for a receipt payment line.
//
function receiptPaymentLine($paydate, $amount, $description='', $method='') {
  $amount = sprintf('%01.2f', $amount); // make it negative
  // Resolve the payment method portion of the memo to display properly.
  if (!empty($method)) {
    $tmp = explode(' ', $method, 2);
    $method = getListTitle('paymethod', $tmp[0]);
    if (isset($tmp[1])) $method .= ' ' . $tmp[1];
  }
  echo " <tr>\n";
  echo "  <td>&nbsp;</td>\n";
  echo "  <td>" . oeFormatShortDate($paydate) . "</td>\n";
  echo "  <td align='center'>$method</td>\n";
  if ($GLOBALS['gbl_checkout_line_adjustments']) {
    echo "  <td>&nbsp;</td>\n";
  }
  echo "  <td>" . xl('Payment') . " $description</td>\n";
  echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1) .
       "' align='right'>" . oeFormatMoney($amount) . "</td>\n";
  echo " </tr>\n";
}

//////////////////////////////////////////////////////////////////////
//
// Generate a receipt from the last-billed invoice for this patient,
// or for the encounter specified as a GET parameter.
//
function generate_receipt($patient_id, $encounter=0) {
  global $css_header, $details, $rapid_data_entry, $aAdjusts;
  global $web_root, $webserver_root;

  // Get the most recent invoice data or that for the specified encounter.
  if ($encounter) {
    $ferow = sqlQuery("SELECT id, date, encounter, facility_id FROM form_encounter " .
      "WHERE pid = '$patient_id' AND encounter = '$encounter'");
  } else {
    $ferow = sqlQuery("SELECT id, date, encounter, facility_id FROM form_encounter " .
      "WHERE pid = '$patient_id' " .
      "ORDER BY id DESC LIMIT 1");
  }
  if (empty($ferow)) die(xl("This patient has no activity."));
  $trans_id = $ferow['id'];
  $encounter = $ferow['encounter'];
  $svcdate = substr($ferow['date'], 0, 10);

  // Get details for the visit's facility.
  $frow = sqlQuery("SELECT f.* FROM facility AS f " .
    "WHERE f.id = '" . $ferow['facility_id'] . "'");

  $patdata = getPatientData($patient_id, 'fname,mname,lname,pubpid,street,city,state,postal_code');

  // Get invoice reference number.
  $encrow = sqlQuery("SELECT invoice_refno FROM form_encounter WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' LIMIT 1");
  $invoice_refno = $encrow['invoice_refno'];
?>
<html>
<head>
<?php html_header_show(); ?>
<link rel='stylesheet' href='<?php echo $css_header ?>' type='text/css'>
<title><?php xl('Client Receipt','e'); ?></title>

<style type="text/css">
body, td {
 font-family: sans-serif;
 font-size: 10pt;
}
</style>

<script type="text/javascript" src="../../library/dialog.js"></script>
<script language="JavaScript">

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

 // Process click on Print button.
 function printme() {
<?php if (!empty($GLOBALS['gbl_custom_receipt'])) { ?>
  // Custom checkout receipt needs to be sent as a PDF in a new window or tab.
  window.open('pos_checkout.php?<?php echo "ptid=$patient_id&enc=$encounter&pdf=1"; ?>',
   '_blank', 'width=750,height=550,resizable=1,scrollbars=1');
<?php } else { ?>
  var divstyle = document.getElementById('hideonprint').style;
  divstyle.display = 'none';
  window.print();
<?php } ?>
  return false;
 }

 // Process click on Delete button.
 function deleteme() {

  // TBD: Make sure deleter.php is still doing the right thing.

  dlgopen('deleter.php?billing=<?php echo "$patient_id.$encounter"; ?>', '_blank', 500, 450);
  return false;
 }

 // Called by the deleteme.php window on a successful delete.
 function imdeleted() {
  window.close();
 }

</script>
</head>
<body class="body_top">
<center>

<table width='95%'>
 <tr>
  <td width='25%' align='left' valign='top'>
<?php
  // TBD: Maybe make a global for this file name.
  $ma_logo_path = "sites/" . $_SESSION['site_id'] . "/images/ma_logo.png";
  if (is_file("$webserver_root/$ma_logo_path")) {
    echo "<img src='$web_root/$ma_logo_path' />";
  }
  else {
    echo "&nbsp;";
  }
?>
  </td>
  <td width='50%' align='center' valign='top'>
   <b><?php echo $frow['name'] ?>
   <br><?php echo $frow['street'] ?>
   <br><?php echo $frow['city'] . ', ' . $frow['state'] . ' ' . $frow['postal_code'] ?>
   <br><?php echo $frow['phone'] ?>
  </td>
  <td width='25%' align='right' valign='top'>
   <!-- This space available. -->
   &nbsp;
  </td>
 </tr>
</table>

<p><b>
<?php
  echo xl("Client Receipt") . '<br />' . dateformat();
  if ($invoice_refno) echo " " . xl("for Invoice") . " $invoice_refno";
?>
<br>&nbsp;
</b></p>

<?php
  // Compute numbers for summary on right side of page.
  $head_begbal = get_patient_balance($patient_id, $encounter);
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1 AND " .
    "code_type != 'COPAY'");
  $head_charges = $row['amount'];
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM drug_sales WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter'");
  $head_charges += $row['amount'];
  $row = sqlQuery("SELECT SUM(pay_amount) AS payments, " .
    "SUM(adj_amount) AS adjustments FROM ar_activity WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter'");
  $head_charges -= $row['adjustments'];
  $head_payments = $row['payments'];
  $row = sqlQuery("SELECT SUM(fee) AS amount FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND activity = 1 AND " .
    "code_type = 'COPAY'");
  $head_payments -= $row['amount'];
  $head_endbal = $head_begbal + $head_charges - $head_payments;
?>
<table width='95%'>
 <tr>
  <td width='50%' align='left' valign='top'>
   <?php echo $patdata['fname'] . ' ' . $patdata['mname'] . ' ' . $patdata['lname']; ?>
   <br><?php echo $patdata['street']; ?>
   <br><?php echo $patdata['city'] . ', ' . $patdata['state'] . ' ' . $patdata['postal_code']; ?>
  </td>
  <td width='50%' align='right' valign='top'>
   <table>
    <tr>
     <td><?php xl('Beginning Account Balance','e'); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td align='right'><?php echo oeFormatMoney($head_begbal); ?></td>
    </tr>
    <tr>
     <td><?php xl('Total Visit Charges','e'); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td align='right'><?php echo oeFormatMoney($head_charges); ?></td>
    </tr>
    <tr>
     <td><?php xl('Payments','e'); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td align='right'><?php echo oeFormatMoney($head_payments); ?></td>
    </tr>
    <tr>
     <td><?php xl('Ending Account Balance','e'); ?>&nbsp;&nbsp;&nbsp;&nbsp;</td>
     <td align='right'><?php echo oeFormatMoney($head_endbal); ?></td>
    </tr>
   </table>
  </td>
 </tr>
</table>

<table cellpadding='2' width='95%'>
<?php if ($details) { ?>
 <tr>
  <td colspan='<?php echo empty($GLOBALS['gbl_checkout_line_adjustments']) ? 5 : 7; ?>'>
    <b><?php echo xl('Today`s Visit'); ?></b><br />&nbsp;
  </td>
 </tr>

 <tr>
  <td colspan='<?php echo empty($GLOBALS['gbl_checkout_line_adjustments']) ? 5 : 7; ?>'
   style='padding-top:5pt;'>
    <b><?php echo xl('Charges'); ?></b>
  </td>
 </tr>

 <tr>
  <td><b><?php xl('Code','e'); ?></b></td>
  <td><b><?php xl('Description','e'); ?></b></td>
  <td align='center'><b><?php echo $details ? xl('Qty'  ) : '&nbsp;'; ?></b></td>
  <td align='right'><b><?php echo $details ? xl('Price') : '&nbsp;'; ?></b></td>
<?php if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) { ?>
  <td align='right'><b><?php xl('Charge','e'); ?></b></td>
  <td align='right'><b><?php xl('Adj','e'); ?></b></td>
<?php } ?>
  <td align='right'><b><?php xl('Total','e'); ?></b></td>
 </tr>
<?php } // end if details ?>

 <tr>
  <td colspan='<?php echo empty($GLOBALS['gbl_checkout_line_adjustments']) ? 5 : 7; ?>'
   style='border-top:1px solid black; font-size:1px; padding:0;'>
    &nbsp;
  </td>
 </tr>

<?php
  // Create array aAdjusts from ar_activity rows for $inv_encounter.
  $aAdjusts = array();
  $ares = sqlStatement("SELECT " .
    "a.payer_type, a.adj_amount, a.memo, a.code_type, a.code, " .
    "s.session_id, s.reference, s.check_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter' AND a.adj_amount != 0");
  while ($arow = sqlFetchArray($ares)) $aAdjusts[] = $arow;

  $aTotals = array(0, 0, 0, 0, 0);

  // Product sales
  $inres = sqlStatement("SELECT s.sale_id, s.sale_date, s.fee, " .
    "s.quantity, s.drug_id, d.name " .
    "FROM drug_sales AS s LEFT JOIN drugs AS d ON d.drug_id = s.drug_id " .
    "WHERE s.pid = '$patient_id' AND s.encounter = '$encounter' " .
    "ORDER BY s.sale_id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptDetailLine('PROD', $inrow['drug_id'], $inrow['name'],
      $inrow['quantity'], $inrow['fee'], $aTotals);
  }

  // Service items.
  $inres = sqlStatement("SELECT * FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type != 'COPAY' AND code_type != 'TAX' AND activity = 1 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptDetailLine($inrow['code_type'], $inrow['code'], $inrow['code_text'],
      $inrow['units'], $inrow['fee'], $aTotals);
  }

  // Write any adjustments left in the aAdjusts array.
  foreach ($aAdjusts as $arow) {
    if ($arow['adj_amount'] == 0) continue;
    $payer = empty($arow['payer_type']) ? 'Pt' : ('Ins' . $arow['payer_type']);
    receiptDetailLine('', xl('Adjustment'), $payer . ' ' . $arow['memo'], 1,
      0 - $arow['adj_amount'], $aTotals);
  }
?>

 <tr>
  <td colspan='<?php echo empty($GLOBALS['gbl_checkout_line_adjustments']) ? 5 : 7; ?>'
   style='border-top:1px solid black; font-size:1px; padding:0;'>
    &nbsp;
  </td>
 </tr>

<?php
  // Sub-Total line with totals of all numeric columns.
  if ($details) {
    echo " <tr>\n";
    echo "  <td colspan='2' align='right'><b>" . xl('Sub-Total') . "</b></td>\n";
    echo "  <td align='center'>" . $aTotals[0] . "</td>\n";
    echo "  <td align='right'>" . oeFormatMoney($aTotals[1]) . "</td>\n";
    if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
      echo "  <td align='right'>" . oeFormatMoney($aTotals[2]) . "</td>\n";
      echo "  <td align='right'>" . oeFormatMoney($aTotals[3]) . "</td>\n";
    }
    echo "  <td align='right'>" . oeFormatMoney($aTotals[4]) . "</td>\n";
    echo " </tr>\n";
  }

  // Tax items.
  $inres = sqlStatement("SELECT * FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type = 'TAX' AND activity = 1 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    receiptDetailLine($inrow['code_type'], $inrow['code'], $inrow['code_text'],
      1, $inrow['fee'], $aTotals);
  }

  // Total Charges line.
  echo " <tr>\n";
  echo "  <td colspan='" . (empty($GLOBALS['gbl_checkout_line_adjustments']) ? 2 : 4) . "'>&nbsp;</td>\n";
  echo "  <td colspan='2' align='right'><b>" . xl('Total Charges') . "</b></td>\n";
  echo "  <td align='right'>" . oeFormatMoney($aTotals[4]) . "</td>\n";
  echo " </tr>\n";
?>

<!--
</table>
<table cellpadding='2' width='95%'>
-->

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 5; ?>' style='padding-top:5pt;'>
    <b><?php echo xl('Payments'); ?></b>
  </td>
 </tr>

 <tr>
  <td>&nbsp;</td>
  <td><b><?php xl('Date of Service','e'); ?></b></td>
  <td align='center'><b><?php xl('Payment Method','e'); ?></b></td>
<?php if ($GLOBALS['gbl_checkout_line_adjustments']) { ?>
  <td>&nbsp;</td>
<?php } ?>
  <td><b><?php xl('Ref No','e'); ?></b></td>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1; ?>' align='right'><b><?php xl('Amount','e'); ?></b></td>
 </tr>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 5; ?>'
   style='border-top:1px solid black; font-size:1px; padding:0;'>
    &nbsp;
  </td>
 </tr>

<?php
  $payments = 0;

  // Get co-pays.
  $inres = sqlStatement("SELECT fee, code_text FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type = 'COPAY' AND activity = 1 AND fee != 0 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    $payments -= sprintf('%01.2f', $inrow['fee']);
    receiptPaymentLine($svcdate, 0 - $inrow['fee'], $inrow['code_text'], 'COPAY');
  }

  // Get other payments.
  $inres = sqlStatement("SELECT " .
    "a.code, a.modifier, a.memo, a.payer_type, a.adj_amount, a.pay_amount, " .
    "s.payer_id, s.reference, s.check_date, s.deposit_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter' AND " .
    "a.pay_amount != 0 " .
    "ORDER BY s.check_date, a.sequence_no");
  $payer = empty($inrow['payer_type']) ? 'Pt' : ('Ins' . $inrow['payer_type']);
  while ($inrow = sqlFetchArray($inres)) {
    $payments += sprintf('%01.2f', $inrow['pay_amount']);
    receiptPaymentLine($svcdate, $inrow['pay_amount'],
      $payer . ' ' . $inrow['reference'], $inrow['memo']);
  }
?>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 5; ?>'
   style='border-top:1px solid black; font-size:1px; padding:0;'>
    &nbsp;
  </td>
 </tr>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 4 : 2; ?>'>&nbsp;</td>
  <td colspan='2' align='right'><b><?php xl('Total Payments','e'); ?></b></td>
  <td align='right'><?php echo oeFormatMoney($payments, true) ?></td>
 </tr>

</table>
</center>

<p>
<?php
  // The user-customizable note.
  if (!empty($GLOBALS['gbl_checkout_receipt_note'])) {
    echo str_repeat('*', 80) . '<br />';
    echo '&nbsp;&nbsp;' . htmlspecialchars($GLOBALS['gbl_checkout_receipt_note']) . '<br />';
    echo str_repeat('*', 80) . '<br />';
  }
?>
</p>

<div id='hideonprint'>
<p>
&nbsp;
<a href='#' onclick='return printme();'><?php xl('Print','e'); ?></a>
<?php if (acl_check('acct','disc')) { ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='#' onclick='return deleteme();'><?php xl('Undo Checkout','e'); ?></a>
<?php } ?>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?php if ($details) { ?>
<a href='pos_checkout.php?details=0&ptid=<?php echo $patient_id; ?>&enc=<?php echo $encounter; ?>'><?php xl('Hide Details','e'); ?></a>
<?php } else { ?>
<a href='pos_checkout.php?details=1&ptid=<?php echo $patient_id; ?>&enc=<?php echo $encounter; ?>'><?php xl('Show Details','e'); ?></a>
<?php } ?>
</p>
</div>

<script language="JavaScript">
 top.restoreSession();

<?php if ($rapid_data_entry && $GLOBALS['concurrent_layout']) { ?>
 parent.left_nav.setRadio('RTop', 'new');
 parent.left_nav.loadFrame('new1', 'RTop', 'new/new.php');
<?php } ?>

<?php if (!empty($GLOBALS['gbl_custom_receipt'])) { ?>
 // Custom checkout receipt needs to be sent as a PDF in a new window or tab.
 printme();
<?php } ?>

</script>

</body>
</html>
<?php

}
// end function generate_receipt()
//
//////////////////////////////////////////////////////////////////////

// Function to write the heading lines for the data entry form.
// This is deferred because we need to know which encounter was chosen.
//
$form_headers_written = false;
function write_form_headers() {
  global $form_headers_written, $patdata, $patient_id, $inv_encounter, $aAdjusts;

  if ($form_headers_written) return;
  $form_headers_written = true;
?>
 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 4; ?>' align='center'>
   <b><?php xl('Patient Checkout for ','e'); ?><?php echo $patdata['fname'] . " " .
   $patdata['lname'] . " (" . $patdata['pubpid'] . ")" ?></b>
   <br />&nbsp;
   <p>
<?php
  $prvbal = get_patient_balance($patient_id, $inv_encounter);
  echo xl('Previous Balance') . '&nbsp;&nbsp;&nbsp;&nbsp;';
  echo "<input type='text' value='" . oeFormatMoney($prvbal) . "' size='6' ";
  echo "style='text-align:right;background-color:transparent' readonly />\n";
  if ($prvbal > 0) {
    echo "&nbsp;<input type='button' value='" . xl('Pay Previous Balance') .
      "' onclick='payprevious()' />\n";
  }
?>
   <br />&nbsp;
   </p>
  </td>
 </tr>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 4; ?>'
   style='border-top:1px solid black; padding-top:5pt;'>
   <b><?php xl('Current Charges','e'); ?></b>
  </td>
 </tr>

 <tr>
  <td><b><?php xl('Date','e'); ?></b></td>
  <td><b><?php xl('Description','e'); ?></b></td>
  <td align='right'><b><?php xl('Quantity','e'); ?></b></td>
<?php if (empty($GLOBALS['gbl_checkout_line_adjustments'])) { ?>
  <td align='right'><b><?php xl('Charge','e'); ?></b></td>
<?php } else { ?>
  <td align='right'><b><?php xl('Price','e'); ?></b></td>
  <td align='right'><b><?php xl('Charge','e'); ?></b></td>
  <td align='right'><b><?php xl('Adjustment','e'); ?></b></td>
  <td align='right'><b><?php xl('Total','e'); ?></b></td>
<?php } ?>
 </tr>
<?php
  // Create array aAdjusts from ar_activity rows for $inv_encounter.
  $ares = sqlStatement("SELECT " .
    "a.payer_type, a.adj_amount, a.memo, a.code_type, a.code, " .
    "s.session_id, s.reference, s.check_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$inv_encounter' AND a.adj_amount != 0 " .
    "ORDER BY s.check_date, a.sequence_no");
  while ($arow = sqlFetchArray($ares)) $aAdjusts[] = $arow;
}

// Function to output a line item for the input form.
//
$totalchg = 0; // totals charges after adjustments
function write_form_line($code_type, $code, $id, $date, $description,
  $amount, $units, $taxrates) {
  global $lino, $totalchg;
  // Write heading rows if that is not already done.
  write_form_headers();
  $amount = sprintf("%01.2f", $amount);
  if (empty($units)) $units = 1;
  $price = sprintf("%01.4f", $amount / $units); // should be even cents, but...
  if (substr($price, -2) === '00') $price = sprintf("%01.2f", $price);
  // if ($code_type == 'COPAY' && !$description) $description = xl('Payment');

  // Total and clear adjustments in aAdjusts matching this line item.
  $adjust = 0;
  if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
    for ($i = 0; $i < count($aAdjusts); ++$i) {
      if ($aAdjusts[$i]['code_type'] == $code_type && $aAdjusts[$i]['code'] == $code && $aAdjusts[$i]['adj_amount'] != 0) {
        $adjust += $aAdjusts[$i]['adj_amount'];
        $aAdjusts[$i]['adj_amount'] = 0;
      }
    }
  }
  $total = sprintf("%01.2f", $amount - $adjust);
  if (empty($GLOBALS['discount_by_money'])) {
    // Convert $adjust to a percentage of the amount, up to 4 decimal places.
    $adjust = round(100 * $adjust / $amount, 4);
  }

  echo " <tr>\n";
  echo "  <td>" . oeFormatShortDate($date);
  echo "<input type='hidden' name='line[$lino][code_type]' value='$code_type'>";
  echo "<input type='hidden' name='line[$lino][code]' value='$code'>";
  echo "<input type='hidden' name='line[$lino][id]' value='$id'>";
  echo "<input type='hidden' name='line[$lino][description]' value='$description'>";
  echo "<input type='hidden' name='line[$lino][taxrates]' value='$taxrates'>";
  echo "<input type='hidden' name='line[$lino][units]' value='$units'>";
  echo "</td>\n";
  echo "  <td>$description</td>";
  echo "  <td align='right'>$units</td>";

  if (empty($GLOBALS['gbl_checkout_line_adjustments'])) {
    echo "  <td align='right'>";
    echo "<input type='hidden' name='line[$lino][price]' value='$price'>";
    echo "<input type='text' name='line[$lino][amount]' value='$total' size='6'";
    echo " style='text-align:right;background-color:transparent' readonly";
    echo " /></td>\n";
  }
  else {
    echo "  <td align='right'>";
    echo "<input type='text' name='line[$lino][price]' value='$price' size='6'";
    echo " style='text-align:right;background-color:transparent' readonly />";
    echo "</td>\n";
    echo "  <td align='right'>";
    echo "<input type='text' name='line[$lino][charge]' value='$amount' size='6'";
    echo " style='text-align:right;background-color:transparent' readonly />";
    echo "</td>\n";
    echo "  <td align='right'>";
    echo "<input type='text' name='line[$lino][adjust]' value='$adjust' size='6'";
    // Modifying discount requires the acct/disc permission.
    if ($code_type == 'TAX' || $code_type == 'COPAY' || !acl_check('acct','disc'))
      echo " style='text-align:right;background-color:transparent' readonly";
    else
      echo " style='text-align:right' maxlength='8' onkeyup='lineDiscountChanged($lino)'";
    echo " /> ";
    echo empty($GLOBALS['discount_by_money']) ? '%' : $GLOBALS['gbl_currency_symbol'];
    echo "</td>\n";
    echo "  <td align='right'>";
    echo "<input type='text' name='line[$lino][amount]' value='$total' size='6'";
    echo " style='text-align:right;background-color:transparent' readonly />";
    echo "</td>\n";
  }

  echo " </tr>\n";
  ++$lino;
  $totalchg += $amount;
}

// Function to output a past payment/adjustment line to the form.
//
function write_old_payment_line($pay_type, $date, $method, $reference, $amount) {
  global $lino;
  // Write heading rows if that is not already done.
  write_form_headers();
  $amount = sprintf("%01.2f", $amount);
  echo " <tr>\n";
  if ($GLOBALS['gbl_checkout_line_adjustments']) {
    echo "  <td>&nbsp;</td>\n";
  }
  echo "  <td>" . htmlspecialchars($pay_type ) . "</td>\n";
  echo "  <td" .
       ($GLOBALS['gbl_checkout_line_adjustments'] ? " colspan='2'" : "") .
       ">" . htmlspecialchars($method) . "</td>\n";
  echo "  <td>" . htmlspecialchars($reference) . "</td>\n";
  echo "  <td align='right'" .
       ($GLOBALS['gbl_checkout_line_adjustments'] ? " colspan='2'" : "") .
       "><input type='text' name='oldpay[$lino][amount]' " .
       "value='$amount' size='6' maxlength='8'";
  echo " style='text-align:right;background-color:transparent' readonly";
  echo "></td>\n";
  echo " </tr>\n";
  ++$lino;
}

// Array of HTML for the 4 or 5 cells of an input payment row.
// "%d" will be replaced by a payment line number on the client side.
//
$aCellHTML = array();
if ($GLOBALS['gbl_checkout_line_adjustments']) {
  $aCellHTML[] = "&nbsp;";
}
$aCellHTML[] = "<span id='paytitle_%d'>" . htmlspecialchars(xl('New Payment')) . "</span>";
$aCellHTML[] = strtr(generate_select_list('payment[%d][method]', 'paymethod', '', '', ''), array("\n" => ""));
$aCellHTML[] = "<input type='text' name='payment[%d][refno]' size='10' />";
$aCellHTML[] = "<input type='text' name='payment[%d][amount]' size='6' style='text-align:right' onkeyup='setComputedValues()' />";

// Create the taxes array.  Key is tax id, value is
// (description, rate, accumulated total).
$taxes = array();
$pres = sqlStatement("SELECT option_id, title, option_value " .
  "FROM list_options WHERE list_id = 'taxrate' ORDER BY seq");
while ($prow = sqlFetchArray($pres)) {
  $taxes[$prow['option_id']] = array($prow['title'], $prow['option_value'], 0);
}

// Mark the tax rates that are referenced in this invoice.
function markTaxes($taxrates) {
  global $taxes;
  $arates = explode(':', $taxrates);
  if (empty($arates)) return;
  foreach ($arates as $value) {
    if (!empty($taxes[$value])) $taxes[$value][2] = '1';
  }
}

$alertmsg = ''; // anything here pops up in an alert box

// If the Save button was clicked...
//
if ($_POST['form_save']) {

  // On a save, do the following:
  // Flag this form's drug_sales and billing items as billed.
  // Post line-level adjustments, replacing any existing ones for the same charges.
  // Post any invoice-level adjustment.
  // Post payments and be careful to use a unique invoice number.
  // Call the generate-receipt function.
  // Exit.

  $form_pid = $_POST['form_pid'];
  $form_encounter = $_POST['form_encounter'];

  // Get the posting date from the form as yyyy-mm-dd.
  $dosdate = date("Y-m-d");
  if (preg_match("/(\d\d\d\d)\D*(\d\d)\D*(\d\d)/", $_POST['form_date'], $matches)) {
    $dosdate = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
  }

  // If there is no associated encounter (i.e. this invoice has only
  // prescriptions) then assign an encounter number of the service
  // date, with an optional suffix to ensure that it's unique.
  //
  if (! $form_encounter) {
    /*****************************************************************
    $form_encounter = substr($dosdate,0,4) . substr($dosdate,5,2) . substr($dosdate,8,2);
    $tmp = '';
    while (true) {
      $ferow = sqlQuery("SELECT id FROM form_encounter WHERE " .
        "pid = '$form_pid' AND encounter = '$form_encounter$tmp'");
      if (empty($ferow)) break;
      $tmp = $tmp ? $tmp + 1 : 1;
    }
    $form_encounter .= $tmp;
    *****************************************************************/
    // The above seems obsolete. Nothing should be sold without an encounter form.
    die("Internal error: Encounter ID is missing!");
  }

  // Delete any TAX rows from billing because they will be recalculated.
  sqlStatement("UPDATE billing SET activity = 0 WHERE " .
    "pid = '$form_pid' AND encounter = '$form_encounter' AND " .
    "code_type = 'TAX'");

  // Clear any existing nonzero adjustments for this encounter.  Normally
  // there should not be any because checkout should only happen once.
  sqlStatement("UPDATE ar_activity SET adj_amount = 0 WHERE " .
    "pid = '$form_pid' AND encounter = '$form_encounter' AND " .
    "adj_amount != 0");

  $form_amount = $_POST['form_amount'];
  $lines = $_POST['line'];

  for ($lino = 0; $lines[$lino]['code_type']; ++$lino) {
    $line = $lines[$lino];
    $code_type = $line['code_type'];
    $code      = $line['code'];
    $id        = $line['id'];
    $amount    = sprintf('%01.2f', trim($line['amount']));

    // If there is an adjustment for this line, insert it.
    if (!empty($GLOBALS['gbl_checkout_line_adjustments'])) {
      $charge = sprintf('%01.2f', trim($line['charge']));
      $adjust = sprintf('%01.2f', $charge - $amount);
      if ($adjust != 0) {
        $memo = xl('Discount');
        $time = date('Y-m-d H:i:s');
        $query = "INSERT INTO ar_activity ( " .
          "pid, encounter, code_type, code, modifier, payer_type, " .
          "post_user, post_time, session_id, memo, adj_amount " .
          ") VALUES ( " .
          "'$form_pid', " .
          "'$form_encounter', " .
          "'$code_type', " .
          "'$code', " .
          "'', " .
          "'0', " .
          "'" . $_SESSION['authUserID'] . "', " .
          "'$time', " .
          "'0', " .
          "'$memo', " .
          "'$adjust' " .
          ")";
        sqlStatement($query);
      }
    }

    /*****************************************************************
    if ($code_type == 'PROD') {
      // Product sales. The fee and encounter ID may have changed.
      $query = "update drug_sales SET fee = '$amount', " .
      "encounter = '$form_encounter', billed = 1 WHERE " .
      "sale_id = '$id'";
      sqlQuery($query);
    }
    else
    *****************************************************************/
    if ($code_type == 'TAX') {
      // We must save taxes somewhere, and in the billing table with
      // a code type of TAX seems easiest.
      // They will have to be stripped back out when building this
      // script's input form.
      addBilling($form_encounter, 'TAX', 'TAX', 'Taxes', $form_pid, 0, 0,
        '', '', $amount, '', '', 1);
    }
    /*****************************************************************
    else {
      // Because there is no insurance here, there is no need for a claims
      // table entry and so we do not call updateClaim().  Note we should not
      // eliminate billed and bill_date from the billing table!
      $query = "UPDATE billing SET fee = '$amount', billed = 1, " .
      "bill_date = NOW() WHERE id = '$id'";
      sqlQuery($query);
    }
    *****************************************************************/
  }

  // Flag the encounter as billed.
  $query = "UPDATE billing SET billed = 1, bill_date = NOW() WHERE " .
    "pid = '$form_pid' AND encounter = '$form_encounter' AND activity = 1";
  sqlQuery($query);
  $query = "update drug_sales SET billed = 1 WHERE " .
    "pid = '$form_pid' AND encounter = '$form_encounter'";
  sqlQuery($query);

  // Post discount.
  if ($_POST['form_discount'] != 0) {
    if ($GLOBALS['discount_by_money']) {
      $amount  = sprintf('%01.2f', trim($_POST['form_discount']));
    }
    else {
      $amount  = sprintf('%01.2f', trim($_POST['form_discount']) * $form_amount / 100);
    }
    $memo = xl('Discount');
    $time = date('Y-m-d H:i:s');
    $query = "INSERT INTO ar_activity ( " .
      "pid, encounter, code, modifier, payer_type, post_user, post_time, " .
      "session_id, memo, adj_amount " .
      ") VALUES ( " .
      "'$form_pid', " .
      "'$form_encounter', " .
      "'', " .
      "'', " .
      "'0', " .
      "'" . $_SESSION['authUserID'] . "', " .
      "'$time', " .
      "'0', " .
      "'$memo', " .
      "'$amount' " .
      ")";
    sqlStatement($query);
  }

  // Post the payments.
  if (is_array($_POST['payment'])) {
    $lines = $_POST['payment'];
    for ($lino = 0; isset($lines[$lino]['amount']); ++$lino) {
      $line = $lines[$lino];
      $amount = sprintf('%01.2f', trim($line['amount']));
      if ($amount != 0.00) {
        $method = $line['method'];
        $refno  = $line['refno'];
        if ($method !== '' && $refno !== '') $method .= " $refno";
        $session_id = 0; // Is this OK?
        arPostPayment($form_pid, $form_encounter, $session_id, $amount, '', 0, $method, 0);
      }
    }
  }

  // If applicable, set the invoice reference number.
  $invoice_refno = '';
  if (isset($_POST['form_irnumber'])) {
    $invoice_refno = formData('form_irnumber', 'P', true);
  }
  else {
    $invoice_refno = add_escape_custom(updateInvoiceRefNumber());
  }
  if ($invoice_refno) {
    sqlStatement("UPDATE form_encounter " .
      "SET invoice_refno = '$invoice_refno' " .
      "WHERE pid = '$form_pid' AND encounter = '$form_encounter'");
  }

  // If appropriate, update the status of the related appointment to
  // "Checked out".
  updateAppointmentStatus($form_pid, $dosdate, '>');

  generate_receipt($form_pid, $form_encounter);
  exit();
}

// If an encounter ID was given, then we must generate a receipt and exit.
//
if (!empty($_GET['enc'])) {
  if (empty($_GET['pdf'])) {
    generate_receipt($patient_id, $_GET['enc']);
  }
  else {
    // PDF receipt is requested. In this case we are probably in a new window.
    require_once($GLOBALS['srcdir'] . "/checkout_receipt_array.inc.php");
    require_once($GLOBALS['OE_SITE_DIR'] . "/checkout_receipt.inc.php");
    generateCheckoutReceipt(generateReceiptArray($patient_id, $_GET['enc']));
  }
  exit();
}

// Get the unbilled billing table items and product sales for
// this patient.

$query = "SELECT id, date, code_type, code, modifier, code_text, " .
  "provider_id, payer_id, units, fee, encounter " .
  "FROM billing WHERE pid = '$patient_id' AND activity = 1 AND " .
  "billed = 0 AND code_type != 'TAX' " .
  "ORDER BY encounter DESC, id ASC";
$bres = sqlStatement($query);

$query = "SELECT s.sale_id, s.sale_date, s.prescription_id, s.fee, " .
  "s.quantity, s.encounter, s.drug_id, d.name, r.provider_id " .
  "FROM drug_sales AS s " .
  "LEFT JOIN drugs AS d ON d.drug_id = s.drug_id " .
  "LEFT OUTER JOIN prescriptions AS r ON r.id = s.prescription_id " .
  "WHERE s.pid = '$patient_id' AND s.billed = 0 " .
  "ORDER BY s.encounter DESC, s.sale_id ASC";
$dres = sqlStatement($query);

// If there are none, just redisplay the last receipt and exit.
//
if (mysql_num_rows($bres) == 0 && mysql_num_rows($dres) == 0) {
  generate_receipt($patient_id);
  exit();
}

// Get the valid practitioners, including those not active.
$arr_users = array();
$ures = sqlStatement("SELECT id, username FROM users WHERE " .
  "( authorized = 1 OR info LIKE '%provider%' ) AND username != ''");
while ($urow = sqlFetchArray($ures)) {
  $arr_users[$urow['id']] = '1';
}

// Now write a data entry form:
// List unbilled billing items (cpt, hcpcs, copays) for the patient.
// List unbilled product sales for the patient.
// Present an editable dollar amount for each line item, a total
// which is also the default value of the input payment amount,
// and OK and Cancel buttons.
?>
<html>
<head>
<link rel='stylesheet' href='<?php echo $css_header ?>' type='text/css'>
<title><?php xl('Patient Checkout','e'); ?></title>
<style>
</style>
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script type="text/javascript" src="../../library/js/jquery-1.2.2.min.js"></script>
<script language="JavaScript">
 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

 // This clears the tax line items in preparation for recomputing taxes.
 function clearTax(visible) {
  var f = document.forms[0];
  for (var lino = 0; true; ++lino) {
   var pfx = 'line[' + lino + ']';
   if (! f[pfx + '[code_type]']) break;
   if (f[pfx + '[code_type]'].value != 'TAX') continue;
   f[pfx + '[price]'].value = '0.00';
   if (visible) f[pfx + '[amount]'].value = '0.00';
  }
 }

 // For a given tax ID and amount, compute the tax on that amount and add it
 // to the "price" (same as "amount") of the corresponding tax line item.
 // Note the tax line items include their "taxrate" to make this easy.
 function addTax(rateid, amount, visible) {
  if (rateid.length == 0) return 0;
  var f = document.forms[0];
  for (var lino = 0; true; ++lino) {
   var pfx = 'line[' + lino + ']';
   if (! f[pfx + '[code_type]']) break;
   if (f[pfx + '[code_type]'].value != 'TAX') continue;
   if (f[pfx + '[code]'].value != rateid) continue;
   var tax = amount * parseFloat(f[pfx + '[taxrates]'].value);
   tax = parseFloat(tax.toFixed(<?php echo $currdecimals ?>));
   var cumtax = parseFloat(f[pfx + '[price]'].value) + tax;
   f[pfx + '[price]'].value  = cumtax.toFixed(<?php echo $currdecimals ?>); // requires JS 1.5
   if (visible) f[pfx + '[amount]'].value = cumtax.toFixed(<?php echo $currdecimals ?>); // requires JS 1.5
   if (isNaN(tax)) alert('Tax rate not numeric at line ' + lino);
   return tax;
  }
  return 0;
 }

 // This mess recomputes total charges and optionally applies a discount.
 // As a side effect the tax line items are recomputed.
 function computeDiscountedTotals(discount, visible) {
  clearTax(visible);
  var f = document.forms[0];
  var total = 0.00;
  for (var lino = 0; f['line[' + lino + '][code_type]']; ++lino) {
   var code_type = f['line[' + lino + '][code_type]'].value;
   // price is price per unit when the form was originally generated.
   // By contrast, amount is the dynamically-generated discounted line total.
   var price = parseFloat(f['line[' + lino + '][price]'].value);
   if (isNaN(price)) alert('Price not numeric at line ' + lino);
   if (code_type == 'COPAY' || code_type == 'TAX') {
    // This works because the tax lines come last.
    total += parseFloat(price.toFixed(<?php echo $currdecimals ?>));
    continue;
   }

   /******************************************************************
   var units = f['line[' + lino + '][units]'].value;
   var amount = price * units;
   amount = parseFloat(amount.toFixed(<?php echo $currdecimals ?>));
   if (visible) f['line[' + lino + '][amount]'].value = amount.toFixed(<?php echo $currdecimals ?>);
   ******************************************************************/
   // The following line replaces the above.
   var amount = f['line[' + lino + '][amount]'].value;

   total += parseFloat(amount);
   var taxrates  = f['line[' + lino + '][taxrates]'].value;
   var taxids = taxrates.split(':');
   for (var j = 0; j < taxids.length; ++j) {
    addTax(taxids[j], amount, visible);
   }
  }
  if (visible) f.totalchg.value = total.toFixed(<?php echo $currdecimals ?>);
  return total - discount;
 }

 // This computes and returns the total of payments.
 function computePaymentTotal() {
  var f = document.forms[0];
  var total = 0.00;
  for (var lino = 0; ('oldpay[' + lino + '][amount]') in f; ++lino) {
   var amount = parseFloat(f['oldpay[' + lino + '][amount]'].value);
   if (isNaN(amount)) continue;
   amount = parseFloat(amount.toFixed(<?php echo $currdecimals ?>));
   total += amount;
  }
  for (var lino = 0; ('payment[' + lino + '][amount]') in f; ++lino) {
   var amount = parseFloat(f['payment[' + lino + '][amount]'].value);
   if (isNaN(amount)) amount = parseFloat(0);
   amount = parseFloat(amount.toFixed(<?php echo $currdecimals ?>));
   total += amount;
   // Set payment row's description to Refund if the amount is negative.
   var title = amount < 0 ? '<?php echo xl('Refund'); ?>' : '<?php echo xl('New payment'); ?>';
   var span = document.getElementById('paytitle_' + lino);
   span.innerHTML = title;
  }
  return total;
 }

 // Recompute default payment amount with any discount applied, but
 // not if there is more than one input payment line.
 // This is called when the discount amount is changed, and initially.
 // As a side effect the tax line items are recomputed and
 // setComputedValues() is called.
 function billingChanged() {
  var f = document.forms[0];
  var discount = parseFloat(f.form_discount.value);
  if (isNaN(discount)) discount = 0;
<?php if (!$GLOBALS['discount_by_money']) { ?>
  // This site discounts by percentage, so convert it to a money amount.
  if (discount > 100) discount = 100;
  if (discount < 0  ) discount = 0;
  discount = 0.01 * discount * computeDiscountedTotals(0, false);
<?php } ?>
  var total = computeDiscountedTotals(discount, true);
  // Get out if there is more than one input payment line.
  if (!('payment[1][amount]' in f)) {
   f['payment[0][amount]'].value = 0;
   total -= computePaymentTotal();
   f['payment[0][amount]'].value = total.toFixed(<?php echo $currdecimals ?>);
  }
  setComputedValues();
  return true;
 }

 // A line item adjustment was changed, so recompute stuff.
 function lineDiscountChanged(lino) {
  var f = document.forms[0];
  var discount = parseFloat(f['line[' + lino + '][adjust]'].value);
  if (isNaN(discount)) discount = 0;
  var charge = parseFloat(f['line[' + lino + '][charge]'].value);
  if (isNaN(charge)) charge = 0;
<?php if (!$GLOBALS['discount_by_money']) { ?>
  // This site discounts by percentage, so convert it to a money amount.
  if (discount > 100) discount = 100;
  if (discount < 0  ) discount = 0;
  discount = 0.01 * discount * charge;
<?php } ?>
  var amount = charge - discount;
  f['line[' + lino + '][amount]'].value = amount.toFixed(<?php echo $currdecimals ?>);
  // alert(f['line[' + lino + '][amount]'].value); // debugging
  return billingChanged();
 }

 // Set Total Payments, Difference and Balance Due when any amount changes.
 function setComputedValues() {
  var f = document.forms[0];
  var payment = computePaymentTotal();
  var difference = computeDiscountedTotals(0, false) - payment;
  var discount = parseFloat(f.form_discount.value);
  if (isNaN(discount)) discount = 0;
<?php if (!$GLOBALS['discount_by_money']) { ?>
  // This site discounts by percentage, so convert it to a money amount.
  if (discount > 100) discount = 100;
  if (discount < 0  ) discount = 0;
  discount = 0.01 * discount * computeDiscountedTotals(0, false);
<?php } ?>
  var balance = difference - discount;
  f.form_totalpay.value = payment.toFixed(<?php echo $currdecimals ?>);
  f.form_difference.value = difference.toFixed(<?php echo $currdecimals ?>);
  f.form_balancedue.value = balance.toFixed(<?php echo $currdecimals ?>);
  return true;
 }

 // This is called when [Compute] is clicked by the user.
 // Computes and sets the discount value from total charges less payment.
 // This also calls setComputedValues() so the balance due will be correct.
 function computeDiscount() {
  var f = document.forms[0];
  var charges = computeDiscountedTotals(0, false);
  var payment = computePaymentTotal();
  var discount = charges - payment;
<?php if (!$GLOBALS['discount_by_money']) { ?>
  // This site discounts by percentage, so convert to that.
  discount = charges ? (100 * discount / charges) : 0;
  f.form_discount.value = discount.toFixed(4);
<?php } else { ?>
  f.form_discount.value = discount.toFixed(<?php echo $currdecimals ?>);
<?php } ?>
  setComputedValues();
  return false;
 }

 // Add a line for entering a payment.
 var paylino = 0;
 function addPayLine() {
  var table = document.getElementById('paytable');
  for (var i = 0; i < table.rows.length; ++i) {
   if (table.rows[i].id == 'totalpay') {
    var row = table.insertRow(i);
    var cell;
<?php
foreach ($aCellHTML as $ix => $html) {
  echo "    var html = \"$html\";\n";
  echo "    cell = row.insertCell(row.cells.length);\n";
  if ($GLOBALS['gbl_checkout_line_adjustments']) {
    if ($ix == 2 || $ix == 4) echo "    cell.colSpan = 2;\n";
  }
  echo "    cell.innerHTML = html.replace(/%d/, paylino);\n";
}
?>
    cell.align = 'right'; // last cell is right-aligned
    ++paylino;
    break;
   }
  }
  return false;
 }
</script>
</head>

<body class="body_top">

<form method='post' action='pos_checkout.php?rde=<?php echo $rapid_data_entry; ?>'>
<input type='hidden' name='form_pid' value='<?php echo $patient_id ?>' />

<center>

<p>
<!--
<table cellspacing='5' width='100%'>
-->
<table cellspacing='5' id='paytable' width='85%'>
<?php
$inv_encounter = '';
$inv_date      = '';
$inv_provider  = 0;
$inv_payer     = 0;
$gcac_related_visit = false;
$gcac_service_provided = false;

// This to save copays from the billing table.
$aCopays = array();

$lino = 0;

// Process billing table items.  Note this includes co-pays.
// Items that are not allowed to have a fee are skipped.
//
while ($brow = sqlFetchArray($bres)) {
  // Skip all but the most recent encounter.
  if ($inv_encounter && $brow['encounter'] != $inv_encounter) continue;

  $thisdate = substr($brow['date'], 0, 10);
  $code_type = $brow['code_type'];

  if (!$inv_encounter) $inv_encounter = $brow['encounter'];
  $inv_payer = $brow['payer_id'];
  if (!$inv_date || $inv_date < $thisdate) $inv_date = $thisdate;

  // Co-pays are saved for later.
  if ($code_type == 'COPAY') {
    $aCopays[] = $brow;
    continue;
  }

  // Collect tax rates, related code and provider ID.
  $taxrates = '';
  $related_code = '';
  if (!empty($code_types[$code_type]['fee'])) {
    $query = "SELECT taxrates, related_code FROM codes WHERE code_type = '" .
      $code_types[$code_type]['id'] . "' AND " .
      "code = '" . $brow['code'] . "' AND ";
    if ($brow['modifier']) {
      $query .= "modifier = '" . $brow['modifier'] . "'";
    } else {
      $query .= "(modifier IS NULL OR modifier = '')";
    }
    $query .= " LIMIT 1";
    $tmp = sqlQuery($query);
    $taxrates = $tmp['taxrates'];
    $related_code = $tmp['related_code'];
    markTaxes($taxrates);
  }

  write_form_line($code_type, $brow['code'], $brow['id'], $thisdate,
    ucfirst(strtolower($brow['code_text'])), $brow['fee'], $brow['units'],
    $taxrates);

  // Custom logic for IPPF to determine if a GCAC issue applies.
  if ($GLOBALS['ippf_specific'] && $related_code) {
    $relcodes = explode(';', $related_code);
    foreach ($relcodes as $codestring) {
      if ($codestring === '') continue;
      list($codetype, $code) = explode(':', $codestring);
      if ($codetype !== 'IPPF') continue;
      if (preg_match('/^25222/', $code)) {
        $gcac_related_visit = true;
        if (preg_match('/^25222[34]/', $code))
          $gcac_service_provided = true;
      }
    }
  }
}

// Process drug sales / products.
//
while ($drow = sqlFetchArray($dres)) {
  // if ($inv_encounter && $drow['encounter'] && $drow['encounter'] != $inv_encounter) continue;
  if ($inv_encounter && $drow['encounter'] != $inv_encounter) continue;

  $thisdate = $drow['sale_date'];
  if (!$inv_encounter) $inv_encounter = $drow['encounter'];

  if (!$inv_provider && !empty($arr_users[$drow['provider_id']]))
    $inv_provider = $drow['provider_id'] + 0;

  if (!$inv_date || $inv_date < $thisdate) $inv_date = $thisdate;

  // Accumulate taxes for this product.
  $tmp = sqlQuery("SELECT taxrates FROM drug_templates WHERE drug_id = '" .
    $drow['drug_id'] . "' ORDER BY selector LIMIT 1");
  // accumTaxes($drow['fee'], $tmp['taxrates']);
  $taxrates = $tmp['taxrates'];
  markTaxes($taxrates);

  write_form_line('PROD', $drow['drug_id'], $drow['sale_id'],
   $thisdate, $drow['name'], $drow['fee'], $drow['quantity'], $taxrates);
}

// Write a form line for each tax that has money, adding to $total.
foreach ($taxes as $key => $value) {
  if ($value[2]) {
    write_form_line('TAX', $key, $key, date('Y-m-d'), $value[0], 0, 1, $value[1]);
  }
}

// Line for total charges.
$totalchg = sprintf("%01.2f", $totalchg);
echo " <tr>\n";
if (empty($GLOBALS['gbl_checkout_line_adjustments'])) {
  echo "  <td colspan='3' align='right'><b>" . xl('Total Charges This Visit') . "</b></td>\n";
  echo "  <td align='right'><input type='text' name='totalchg' " .
       "value='$totalchg' size='6' maxlength='8' " .
       "style='text-align:right;background-color:transparent' readonly";
  echo "></td>\n";
}
else {
  echo "  <td colspan='4' align='right'><b>" . xl('Total Charges This Visit') . "</b></td>\n";
  echo "  <td align='right'><input type='text' name='totalcba' " .
       "value='$totalchg' size='6' maxlength='8' " .
       "style='text-align:right;background-color:transparent' readonly";
  echo "></td>\n";
  // Note $totalchg is the total of charges before adjustments, and the following
  // field will be recomputed at onload time and as adjustments are entered.
  echo "  <td colspan='2' align='right'><input type='text' name='totalchg' " .
       "value='$totalchg' size='6' maxlength='8' " .
       "style='text-align:right;background-color:transparent' readonly";
  echo "></td>\n";
}
echo " </tr>\n";
?>

<!--
</table>
<table cellspacing='5' id='paytable' width='100%'>
-->

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 4; ?>'
   style='border-top:1px solid black; padding-top:5pt;'>
   <b><?php xl('Payments','e'); ?></b>
  </td>
 </tr>

<?php
// Start new section for payments.
echo "  <tr>\n";
if ($GLOBALS['gbl_checkout_line_adjustments']) {
  echo "   <td>&nbsp;</td>\n";
  echo "   <td><b>" . xl('Type') . "</b></td>\n";
  echo "   <td colspan='2'><b>" . xl('Payment Method') . "</b></td>\n";
  echo "   <td><b>" . xl('Reference') . "</b></td>\n";
  echo "   <td colspan='2' align='right' nowrap><b>" . xl('Payment Amount') . "</b></td>\n";
}
else {
  echo "   <td><b>" . xl('Type') . "</b></td>\n";
  echo "   <td><b>" . xl('Payment Method') . "</b></td>\n";
  echo "   <td><b>" . xl('Reference') . "</b></td>\n";
  echo "   <td align='right'><b>" . xl('Payment Amount') . "</b></td>\n";
}
echo "  </tr>\n";

$lino = 0;

// Write co-pays.
foreach ($aCopays as $brow) {
  $thisdate = substr($brow['date'], 0, 10);
  write_old_payment_line(xl('Prepayment'), $thisdate, $brow['code_text'], '', 0 - $brow['fee']);
}

// Write any adjustments left in the aAdjusts array.
foreach ($aAdjusts as $arow) {
  if ($arow['adj_amount'] == 0) continue;
  $memo = $arow['memo'];
  $reference = $arow['reference'];
  if (empty($arow['session_id'])) {
    $atmp = explode(' ', $memo, 2);
    $memo = $atmp[0];
    $reference = $atmp[1];
  }
  write_old_payment_line(xl('Adjustment'), $thisdate, $memo, $reference, $arow['adj_amount']);
}

// Write ar_activity payments.
$ares = sqlStatement("SELECT " .
  "a.payer_type, a.pay_amount, a.memo, s.session_id, s.reference, s.check_date " .
  "FROM ar_activity AS a " .
  "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
  "a.pid = '$patient_id' AND a.encounter = '$inv_encounter' AND a.pay_amount != 0 " .
  "ORDER BY s.check_date, a.sequence_no");
while ($arow = sqlFetchArray($ares)) {
  $memo = $arow['memo'];
  $reference = $arow['reference'];
  if (empty($arow['session_id'])) {
    $atmp = explode(' ', $memo, 2);
    $memo = $atmp[0];
    $reference = $atmp[1];
  }
  $rowtype = $arow['payer_type'] ? xl('Insurance payment') : xl('Prepayment');
  write_old_payment_line($rowtype, $thisdate, $memo, $reference, $arow['pay_amount']);
}

// Line for total payments.
echo " <tr id='totalpay'>\n";
if ($GLOBALS['gbl_checkout_line_adjustments']) {
  echo "  <td>&nbsp;</td>\n";
}
echo "  <td><a href='#' onclick='return addPayLine()'>[" . xl('Add Row') . "]</a></td>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? "3" : "2") .
     "' align='right'><b>" . xl('Total Payments This Visit') . "</b></td>\n";
echo "  <td align='right'" .
     ($GLOBALS['gbl_checkout_line_adjustments'] ? " colspan='2'" : "") .
     "><input type='text' name='form_totalpay' " .
     "value='$amount' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";

// Line for Difference.
echo "  <tr>\n";
echo "   <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 4) .
     "' style='border-top:1px solid black; font-size:1pt; padding:0px;'>&nbsp;</td>\n";
echo "  </tr>\n";
echo " <tr>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3) .
     "' align='right'><b>" . xl('Difference') . "</b></td>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1) .
     "' align='right'><input type='text' name='form_difference' " .
     "value='' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";

if ($inv_encounter) {
  $erow = sqlQuery("SELECT provider_id FROM form_encounter WHERE " .
    "pid = '$patient_id' AND encounter = '$inv_encounter' " .
    "ORDER BY id DESC LIMIT 1");
  $inv_provider = $erow['provider_id'] + 0;
}

// Line for Discount.
echo " <tr>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3) .
     "' align='right'>";
echo "<a href='#' onclick='return computeDiscount()'>[" . xl('Compute') ."]</a> <b>";
echo xl('Discount/Adjustment') . "</b></td>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1) .
     "' align='right'><input type='text' name='form_discount' " .
     "value='' size='6' maxlength='8' onkeyup='billingChanged()' " .
     "style='text-align:right'";
echo "></td>\n";
echo " </tr>\n";

// Line for Balance Due
echo " <tr>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3) .
     "' align='right'><b>" . xl('Balance Due') . "</b></td>\n";
echo "  <td colspan='" . ($GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1) .
     "' align='right'><input type='text' name='form_balancedue' " .
     "value='' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";
?>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3; ?>' align='right'>
   <b><?php xl('Posting Date','e'); ?></b>
  </td>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1; ?>' align='right'>
   <input type='text' size='10' name='form_date' id='form_date'
    value='<?php echo $inv_date ?>'
    title='yyyy-mm-dd date of service'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' />
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_date' border='0' alt='[?]' style='cursor:pointer'
    title='Click here to choose a date'>
  </td>
 </tr>

<?php
// If this user has a non-empty irnpool assigned, show the pending
// invoice reference number.
$irnumber = getInvoiceRefNumber();
if (!empty($irnumber)) {
?>
 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3; ?>' align='right'>
   <b><?php xl('Tentative Invoice Ref No','e'); ?></b>
  </td>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1; ?>' align='right'>
   <?php echo $irnumber; ?>
  </td>
 </tr>
<?php
}
// Otherwise if there is an invoice reference number mask, ask for the refno.
else if (!empty($GLOBALS['gbl_mask_invoice_number'])) {
?>
 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 5 : 3; ?>' align='right'>
   <b><?php xl('Invoice Reference Number','e'); ?></b>
  </td>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 2 : 1; ?>' align='right'>
   <input type='text' name='form_irnumber' size='10' value=''
    onkeyup='maskkeyup(this,"<?php echo addslashes($GLOBALS['gbl_mask_invoice_number']); ?>")'
    onblur='maskblur(this,"<?php echo addslashes($GLOBALS['gbl_mask_invoice_number']); ?>")'
    />
  </td>
 </tr>
<?php
}
?>

 <tr>
  <td colspan='<?php echo $GLOBALS['gbl_checkout_line_adjustments'] ? 7 : 4; ?>' align='center'>
   &nbsp;<br>
   <input type='submit' name='form_save' value='<?php xl('Save','e'); ?>'
<?php if ($rapid_data_entry) echo "    style='background-color:#cc0000';color:#ffffff'"; ?>
   /> &nbsp;
<?php if (empty($_GET['framed'])) { ?>
   <input type='button' value='Cancel' onclick='window.close()' />
<?php } ?>
   <input type='hidden' name='form_provider'  value='<?php echo $inv_provider  ?>' />
   <input type='hidden' name='form_payer'     value='<?php echo $inv_payer     ?>' />
   <input type='hidden' name='form_encounter' value='<?php echo $inv_encounter ?>' />
  </td>
 </tr>

</table>

</center>

</form>

<script language='JavaScript'>

 // Pop up the Payments window and close this one.
 function payprevious() {
  var width  = 750;
  var height = 550;
  var loc = '../patient_file/front_payment.php?omitenc=<?php echo $inv_encounter; ?>';
<?php if (empty($_GET['framed'])) { ?>
  opener.parent.left_nav.dlgopen(loc, '_blank', width, height);
  window.close();
<?php } else { ?>
  parent.left_nav.dlgopen(loc, '_blank', width, height);
<?php } ?>
 }

 Calendar.setup({inputField:"form_date", ifFormat:"%Y-%m-%d", button:"img_date"});
 addPayLine();
 billingChanged();
<?php
if ($gcac_related_visit && !$gcac_service_provided) {
  // Skip this warning if the GCAC visit form is not allowed.
  $grow = sqlQuery("SELECT COUNT(*) AS count FROM list_options " .
    "WHERE list_id = 'lbfnames' AND option_id = 'LBFgcac'");
  if (!empty($grow['count'])) { // if gcac is used
    // Skip this warning if referral or abortion in TS.
    $grow = sqlQuery("SELECT COUNT(*) AS count FROM transactions " .
      "WHERE title = 'Referral' AND refer_date IS NOT NULL AND " .
      "refer_date = '$inv_date' AND pid = '$patient_id'");
    if (empty($grow['count'])) { // if there is no referral
      $grow = sqlQuery("SELECT COUNT(*) AS count FROM forms " .
        "WHERE pid = '$patient_id' AND encounter = '$inv_encounter' AND " .
        "deleted = 0 AND formdir = 'LBFgcac'");
      if (empty($grow['count'])) { // if there is no gcac form
        echo " alert('" . xl('This visit will need a GCAC form, referral or procedure service.') . "');\n";
      }
    }
  }
} // end if ($gcac_related_visit)

if ($GLOBALS['ippf_specific']) {
  // More validation:
  // o If there is an initial contraceptive consult, make sure a LBFccicon form exists with that method on it.
  // o If a LBFccicon form exists with a new method on it, make sure the TS initial consult exists.

  require_once("$srcdir/contraception_billing_scan.inc.php");
  contraception_billing_scan($patient_id, $inv_encounter);

  $csrow = sqlQuery("SELECT f.form_id, ld.field_value FROM forms AS f " .
    "LEFT JOIN lbf_data AS ld ON ld.form_id = f.form_id AND ld.field_id = 'newmethod' " .
    "WHERE " .
    "f.pid = '$patient_id' AND f.encounter = '$inv_encounter' AND " .
    "f.formdir = 'LBFccicon' AND f.deleted = 0 " .
    "ORDER BY f.form_id DESC LIMIT 1");
  $csmethod = empty($csrow['field_value']) ? '' : $csrow['field_value'];

  if (($csmethod || $contraception_billing_code) && $csmethod != $contraception_billing_code) {
    echo " alert('" . xl('Warning') . ': ';
    if (!$csmethod) {
      echo xl('there is a contraception service but no contraception form new method');
    }
    else if (!$contraception_billing_code) {
      echo xl('there is a contraception form new method but no contraception service');
    }
    else {
      echo xl('new method in contraception form does not match the contraception service');
    }
    echo "');\n";
  }
}
?>
</script>

</body>
</html>


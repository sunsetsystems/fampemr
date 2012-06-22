<?php
// Copyright (C) 2006-2012 Rod Roark <rod@sunsetsystems.com>
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

$details = empty($_GET['details']) ? 0 : 1;

$patient_id = empty($_GET['ptid']) ? $pid : 0 + $_GET['ptid'];

// This flag comes from the Fee Sheet form and perhaps later others.
$rapid_data_entry = empty($_GET['rde']) ? 0 : 1;

// Get the patient's name and chart number.
$patdata = getPatientData($patient_id, 'fname,mname,lname,pubpid,street,city,state,postal_code');

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

// Output HTML for an invoice line item.
//
$prevsvcdate = '';
function receiptDetailLine($svcdate, $description, $amount, $quantity) {
  global $prevsvcdate, $details;
  if (!$details) return;
  $amount = sprintf('%01.2f', $amount);
  if (empty($quantity)) $quantity = 1;
  $price = sprintf('%01.4f', $amount / $quantity);
  $tmp = sprintf('%01.2f', $price);
  if ($price == $tmp) $price = $tmp;
  echo " <tr>\n";
  echo "  <td>" . ($svcdate == $prevsvcdate ? '&nbsp;' : oeFormatShortDate($svcdate)) . "</td>\n";
  echo "  <td>$description</td>\n";
  echo "  <td align='right'>" . oeFormatMoney($price) . "</td>\n";
  echo "  <td align='right'>$quantity</td>\n";
  echo "  <td align='right'>" . oeFormatMoney($amount) . "</td>\n";
  echo " </tr>\n";
  $prevsvcdate = $svcdate;
}

// Output HTML for an invoice payment.
//
function receiptPaymentLine($paydate, $amount, $description='') {
  $amount = sprintf('%01.2f', 0 - $amount); // make it negative
  echo " <tr>\n";
  echo "  <td>" . oeFormatShortDate($paydate) . "</td>\n";
  echo "  <td>" . xl('Payment') . " $description</td>\n";
  echo "  <td colspan='2'>&nbsp;</td>\n";
  echo "  <td align='right'>" . oeFormatMoney($amount) . "</td>\n";
  echo " </tr>\n";
}

//////////////////////////////////////////////////////////////////////
//
// Generate a receipt from the last-billed invoice for this patient,
// or for the encounter specified as a GET parameter.
//
function generate_receipt($patient_id, $encounter=0) {
  global $css_header, $details, $rapid_data_entry;

  // Get details for what we guess is the primary facility.
  $frow = sqlQuery("SELECT * FROM facility " .
    "ORDER BY billing_location DESC, accepts_assignment DESC, id LIMIT 1");

  $patdata = getPatientData($patient_id, 'fname,mname,lname,pubpid,street,city,state,postal_code');

  // Get the most recent invoice data or that for the specified encounter.
  //
  if ($encounter) {
    $ferow = sqlQuery("SELECT id, date, encounter FROM form_encounter " .
      "WHERE pid = '$patient_id' AND encounter = '$encounter'");
  } else {
    $ferow = sqlQuery("SELECT id, date, encounter FROM form_encounter " .
      "WHERE pid = '$patient_id' " .
      "ORDER BY id DESC LIMIT 1");
  }
  if (empty($ferow)) die(xl("This patient has no activity."));
  $trans_id = $ferow['id'];
  $encounter = $ferow['encounter'];
  $svcdate = substr($ferow['date'], 0, 10);

  // Get invoice reference number.
  $encrow = sqlQuery("SELECT invoice_refno FROM form_encounter WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' LIMIT 1");
  $invoice_refno = $encrow['invoice_refno'];
?>
<html>
<head>
<?php html_header_show(); ?>
<link rel='stylesheet' href='<?php echo $css_header ?>' type='text/css'>
<title><?php xl('Receipt for Payment','e'); ?></title>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script language="JavaScript">

<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>

 // Process click on Print button.
 function printme() {
  var divstyle = document.getElementById('hideonprint').style;
  divstyle.display = 'none';
  window.print();
  return false;
 }

 // Process click on Delete button.
 function deleteme() {
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
<p><b><?php echo $frow['name'] ?>
<br><?php echo $frow['street'] ?>
<br><?php echo $frow['city'] . ', ' . $frow['state'] . ' ' . $frow['postal_code'] ?>
<br><?php echo $frow['phone'] ?>
<br>&nbsp;
<br>
<?php
  echo xl("Receipt Generated") . ' ' . dateformat();
  if ($invoice_refno) echo " " . xl("for Invoice") . " $invoice_refno";
?>
<br>&nbsp;
</b></p>
</center>
<p>
<?php echo $patdata['fname'] . ' ' . $patdata['mname'] . ' ' . $patdata['lname'] ?>
<br><?php echo $patdata['street'] ?>
<br><?php echo $patdata['city'] . ', ' . $patdata['state'] . ' ' . $patdata['postal_code'] ?>
<br>&nbsp;
</p>
<center>
<table cellpadding='5'>
<?php if ($details) { ?>
 <tr>
  <td><b><?php xl('Date','e'); ?></b></td>
  <td><b><?php xl('Description','e'); ?></b></td>
  <td align='right'><b><?php echo $details ? xl('Price') : '&nbsp;'; ?></b></td>
  <td align='right'><b><?php echo $details ? xl('Qty'  ) : '&nbsp;'; ?></b></td>
  <td align='right'><b><?php xl('Total','e'); ?></b></td>
 </tr>
<?php } ?>

<?php
  $charges = 0.00;

  // Product sales
  $inres = sqlStatement("SELECT s.sale_id, s.sale_date, s.fee, " .
    "s.quantity, s.drug_id, d.name " .
    "FROM drug_sales AS s LEFT JOIN drugs AS d ON d.drug_id = s.drug_id " .
    // "WHERE s.pid = '$patient_id' AND s.encounter = '$encounter' AND s.fee != 0 " .
    "WHERE s.pid = '$patient_id' AND s.encounter = '$encounter' " .
    "ORDER BY s.sale_id");
  while ($inrow = sqlFetchArray($inres)) {
    $charges += sprintf('%01.2f', $inrow['fee']);
    receiptDetailLine($inrow['sale_date'], $inrow['name'],
      $inrow['fee'], $inrow['quantity']);
  }
  // Service and tax items
  $inres = sqlStatement("SELECT * FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    // "code_type != 'COPAY' AND activity = 1 AND fee != 0 " .
    "code_type != 'COPAY' AND activity = 1 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    $charges += sprintf('%01.2f', $inrow['fee']);
    receiptDetailLine($svcdate, $inrow['code_text'],
      $inrow['fee'], $inrow['units']);
  }
  // Adjustments.
  $inres = sqlStatement("SELECT " .
    "a.code, a.modifier, a.memo, a.payer_type, a.adj_amount, a.pay_amount, " .
    "s.payer_id, s.reference, s.check_date, s.deposit_date " .
    "FROM ar_activity AS a " .
    "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter' AND " .
    "a.adj_amount != 0 " .
    "ORDER BY s.check_date, a.sequence_no");
  while ($inrow = sqlFetchArray($inres)) {
    $charges -= sprintf('%01.2f', $inrow['adj_amount']);
    $payer = empty($inrow['payer_type']) ? 'Pt' : ('Ins' . $inrow['payer_type']);
    receiptDetailLine($svcdate, $payer . ' ' . $inrow['memo'],
      0 - $inrow['adj_amount'], 1);
  }
?>

 <tr>
  <td colspan='5'>&nbsp;</td>
 </tr>
 <tr>
  <td><?php echo oeFormatShortDate($svcdispdate); ?></td>
  <td><b><?php xl('Total Charges','e'); ?></b></td>
  <td align='right'>&nbsp;</td>
  <td align='right'>&nbsp;</td>
  <td align='right'><?php echo oeFormatMoney($charges, true) ?></td>
 </tr>
 <tr>
  <td colspan='5'>&nbsp;</td>
 </tr>

<?php
  // Get co-pays.
  $inres = sqlStatement("SELECT fee, code_text FROM billing WHERE " .
    "pid = '$patient_id' AND encounter = '$encounter' AND " .
    "code_type = 'COPAY' AND activity = 1 AND fee != 0 " .
    "ORDER BY id");
  while ($inrow = sqlFetchArray($inres)) {
    $charges += sprintf('%01.2f', $inrow['fee']);
    receiptPaymentLine($svcdate, 0 - $inrow['fee'], $inrow['code_text']);
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
    $charges -= sprintf('%01.2f', $inrow['pay_amount']);
    receiptPaymentLine($svcdate, $inrow['pay_amount'],
      $payer . ' ' . $inrow['reference']);
  }
?>
 <tr>
  <td colspan='5'>&nbsp;</td>
 </tr>
 <tr>
  <td>&nbsp;</td>
  <td><b><?php xl('Balance Due','e'); ?></b></td>
  <td colspan='2'>&nbsp;</td>
  <td align='right'><?php echo oeFormatMoney($charges, true) ?></td>
 </tr>
</table>
</center>
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

<?php if ($rapid_data_entry && $GLOBALS['concurrent_layout']) { ?>
<script language="JavaScript">
 top.restoreSession();
 parent.left_nav.setRadio('RTop', 'new');
 parent.left_nav.loadFrame('new1', 'RTop', 'new/new.php');
</script>
<?php } ?>

</body>
</html>
<?php

}
// end function generate_receipt()
//
//////////////////////////////////////////////////////////////////////

// Function to output a line item for the input form.
//
$totalchg = 0;
function write_form_line($code_type, $code, $id, $date, $description,
  $amount, $units, $taxrates) {
  global $lino, $totalchg;
  $amount = sprintf("%01.2f", $amount);
  if (empty($units)) $units = 1;
  $price = $amount / $units; // should be even cents, but ok here if not
  // if ($code_type == 'COPAY' && !$description) $description = xl('Payment');
  echo " <tr>\n";
  echo "  <td>" . oeFormatShortDate($date);
  echo "<input type='hidden' name='line[$lino][code_type]' value='$code_type'>";
  echo "<input type='hidden' name='line[$lino][code]' value='$code'>";
  echo "<input type='hidden' name='line[$lino][id]' value='$id'>";
  echo "<input type='hidden' name='line[$lino][description]' value='$description'>";
  echo "<input type='hidden' name='line[$lino][taxrates]' value='$taxrates'>";
  echo "<input type='hidden' name='line[$lino][price]' value='$price'>";
  echo "<input type='hidden' name='line[$lino][units]' value='$units'>";
  echo "</td>\n";
  echo "  <td>$description</td>";
  echo "  <td align='right'>$units</td>";
  // While this is an input field due to old logic, it's always read-only now.
  echo "  <td align='right'><input type='text' name='line[$lino][amount]' " .
       "value='$amount' size='6' maxlength='8'";
  // Modifying prices requires the acct/disc permission.
  // if ($code_type == 'TAX' || ($code_type != 'COPAY' && !acl_check('acct','disc')))
  echo " style='text-align:right;background-color:transparent' readonly";
  // else echo " style='text-align:right' onkeyup='billingChanged()'";
  echo "></td>\n";
  echo " </tr>\n";
  ++$lino;
  $totalchg += $amount;
}



// Function to output a past payment/adjustment line to the form.
//
function write_old_payment_line($pay_type, $date, $method, $reference, $amount) {
  global $lino;
  $amount = sprintf("%01.2f", $amount);
  echo " <tr>\n";
  echo "  <td>" . htmlspecialchars($pay_type ) . "</td>\n";
  echo "  <td>" . htmlspecialchars($method   ) . "</td>\n";
  echo "  <td>" . htmlspecialchars($reference) . "</td>\n";
  echo "  <td align='right'><input type='text' name='oldpay[$lino][amount]' " .
       "value='$amount' size='6' maxlength='8'";
  echo " style='text-align:right;background-color:transparent' readonly";
  echo "></td>\n";
  echo " </tr>\n";
  ++$lino;
}

// Array of HTML for the 4 cells of an input payment row.
// "%d" will be replaced by a payment line number on the client side.
//
$aCellHTML = array(
  htmlspecialchars(xl('New Payment')),
  strtr(generate_select_list('payment[%d][method]', 'paymethod', '', '', ''), array("\n" => "")),
  "<input type='text' name='payment[%d][refno]' size='10' />",
  "<input type='text' name='payment[%d][amount]' size='6' style='text-align:right' onkeyup='setComputedValues()' />",
);



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

  $form_amount = $_POST['form_amount'];
  $lines = $_POST['line'];

  for ($lino = 0; $lines[$lino]['code_type']; ++$lino) {
    $line = $lines[$lino];
    $code_type = $line['code_type'];
    $id        = $line['id'];
    $amount    = sprintf('%01.2f', trim($line['amount']));

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
  if ($_POST['form_discount']) {
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



  /*******************************************************************
  // Post payment.
  if ($_POST['form_amount']) {
    $amount  = sprintf('%01.2f', trim($_POST['form_amount']));
    $form_source = trim($_POST['form_source']);
    $paydesc = trim($_POST['form_method']);
    // Post the payment as a billed copay into the billing table.
    // Maybe this should even be done for the SL case.
    if (!empty($form_source)) $paydesc .= " $form_source";
    addBilling($form_encounter, 'COPAY', $amount, $paydesc, $form_pid,
      0, 0, '', '', 0 - $amount, '', '', 1);
  }
  *******************************************************************/

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

// If an encounter ID was given, then we must generate a receipt.
//
if (!empty($_GET['enc'])) {
  generate_receipt($patient_id, $_GET['enc']);
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
   var units = f['line[' + lino + '][units]'].value;
   var amount = price * units;
   amount = parseFloat(amount.toFixed(<?php echo $currdecimals ?>));
   if (visible) f['line[' + lino + '][amount]'].value = amount.toFixed(<?php echo $currdecimals ?>);
   total += amount;
   var taxrates  = f['line[' + lino + '][taxrates]'].value;
   var taxids = taxrates.split(':');
   for (var j = 0; j < taxids.length; ++j) {
    addTax(taxids[j], amount, visible);
   }
  }
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
   if (isNaN(amount)) continue;
   amount = parseFloat(amount.toFixed(<?php echo $currdecimals ?>));
   total += amount;
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
  echo "    cell = row.insertCell($ix);\n";
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
<table cellspacing='5' id='paytable'>
 <tr>
  <td colspan='4' align='center'>
   <b><?php xl('Patient Checkout for ','e'); ?><?php echo $patdata['fname'] . " " .
    $patdata['lname'] . " (" . $patdata['pubpid'] . ")" ?></b>
    <br />&nbsp;
  </td>
 </tr>
 <tr>
  <td><b><?php xl('Date','e'); ?></b></td>
  <td><b><?php xl('Description','e'); ?></b></td>
  <td align='right'><b><?php xl('Quantity','e'); ?></b></td>
  <td align='right'><b><?php xl('Charge Amount','e'); ?></b></td>
 </tr>
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
  if (!$inv_encounter) $inv_encounter = $brow['encounter'];
  $inv_payer = $brow['payer_id'];
  if (!$inv_date || $inv_date < $thisdate) $inv_date = $thisdate;

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
  if ($inv_encounter && $drow['encounter'] && $drow['encounter'] != $inv_encounter) continue;

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
echo "  <td colspan='3' align='right'><b>" . xl('Total charges this visit') . "</b></td>\n";
echo "  <td align='right'><input type='text' name='totalchg' " .
     "value='$totalchg' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";



// Start new section for payments.
echo "  <tr>\n";
echo "   <td colspan='4'>&nbsp;</td>\n";
echo "  </tr>\n";
echo "  <tr>\n";
echo "   <td><b>" . xl('Type') . "</b></td>\n";
echo "   <td><b>" . xl('Payment Method') . "</b></td>\n";
echo "   <td><b>" . xl('Reference') . "</b></td>\n";
echo "   <td align='right'><b>" . xl('Payment Amount') . "</b></td>\n";
echo "  </tr>\n";

$lino = 0;

// Write co-pays.
foreach ($aCopays as $brow) {
  $thisdate = substr($brow['date'], 0, 10);
  write_old_payment_line(xl('Prepayment'), $thisdate, $brow['code_text'], '', 0 - $brow['fee']);
}

// Write ar_activity payments and adjustments.
$ares = sqlStatement("SELECT " .
  "a.payer_type, a.adj_amount, a.pay_amount, a.memo, " .
  "s.session_id, s.reference, s.check_date " .
  "FROM ar_activity AS a " .
  "LEFT JOIN ar_session AS s ON s.session_id = a.session_id WHERE " .
  "a.pid = '$patient_id' AND a.encounter = '$encounter' " .
  "ORDER BY s.check_date, a.sequence_no");
while ($arow = sqlFetchArray($ares)) {
  $memo = $arow['memo'];
  $reference = $arow['reference'];
  if (empty($arow['session_id'])) {
    $atmp = explode(' ', $memo, 2);
    $memo = $atmp[0];
    $reference = $atmp[1];
  }
  if ($arow['pay_amount'] != 0) {
    $rowtype = $arow['payer_type'] ? xl('Insurance payment') : xl('Patient payment');
    write_old_payment_line($rowtype, $thisdate, $memo, $reference, $arow['pay_amount']);
  }
  if ($arow['adj_amount'] != 0) {
    write_old_payment_line(xl('Adjustment'), $thisdate, $memo, $reference, $arow['adj_amount']);
  }
}



// Line for total payments.
echo " <tr id='totalpay'>\n";
echo "  <td><a href='#' onclick='return addPayLine()'>[" . xl('Add Row') . "]</a></td>\n";
echo "  <td colspan='2' align='right'><b>" . xl('Total payments this visit') . "</b></td>\n";
echo "  <td align='right'><input type='text' name='form_totalpay' " .
     "value='$amount' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";



// Line for Difference.
echo "  <tr>\n";
echo "   <td colspan='4'>&nbsp;</td>\n";
echo "  </tr>\n";
echo " <tr>\n";
echo "  <td colspan='3' align='right'><b>" . xl('Difference') . "</b></td>\n";
echo "  <td align='right'><input type='text' name='form_difference' " .
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
echo "  <td colspan='3' align='right'>";
echo "<a href='#' onclick='return computeDiscount()'>[" . xl('Compute') ."]</a> <b>";
echo xl('Discount/Adjustment') . "</b></td>\n";
echo "  <td align='right'><input type='text' name='form_discount' " .
     "value='' size='6' maxlength='8' onkeyup='billingChanged()' " .
     "style='text-align:right'";
echo "></td>\n";
echo " </tr>\n";

// Line for Balance Due
echo " <tr>\n";
echo "  <td colspan='3' align='right'><b>" . xl('Balance Due') . "</b></td>\n";
echo "  <td align='right'><input type='text' name='form_balancedue' " .
     "value='' size='6' maxlength='8' " .
     "style='text-align:right;background-color:transparent' readonly";
echo "></td>\n";
echo " </tr>\n";



?>



<!--

</table>
<p>
<table border='0' cellspacing='4'>

 <tr>
  <td>
   <?php echo $GLOBALS['discount_by_money'] ? xl('Discount Amount') : xl('Discount Percentage'); ?>:
  </td>
  <td>
   <input type='text' name='form_discount' size='6' maxlength='8' value=''
    style='text-align:right' onkeyup='billingChanged()'>
    &nbsp;
   <a href='#' onclick='return computeDiscount()'>[<?php xl('Compute','e'); ?>]</a>
  </td>
 </tr>

 <tr>
  <td>
   <?php xl('Payment Method','e'); ?>:
  </td>
  <td>
<?php
 // echo generate_select_list('form_method', 'paymethod', '', '', '');
?>
  </td>
 </tr>

 <tr>
  <td>
   <?php xl('Check/Reference Number','e'); ?>:
  </td>
  <td>
   <input type='text' name='form_source' size='10' value=''>
  </td>
 </tr>

 <tr>
  <td>
   <?php xl('Amount Paid','e'); ?>:
  </td>
  <td>
   <input type='text' name='form_amount' size='10' value='0.00'>
  </td>
 </tr>

-->



 <tr>
  <td colspan='3' align='right'>
   <b><?php xl('Posting Date','e'); ?></b>
  </td>
  <td align='right'>
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
  <td colspan='3' align='right'>
   <b><?php xl('Tentative Invoice Ref No','e'); ?></b>
  </td>
  <td align='right'>
   <?php echo $irnumber; ?>
  </td>
 </tr>
<?php
}
// Otherwise if there is an invoice reference number mask, ask for the refno.
else if (!empty($GLOBALS['gbl_mask_invoice_number'])) {
?>
 <tr>
  <td colspan='3' align='right'>
   <b><?php xl('Invoice Reference Number','e'); ?></b>
  </td>
  <td align='right'>
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
  <td colspan='4' align='center'>
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
?>
</script>

</body>
</html>


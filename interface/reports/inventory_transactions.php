<?php
// Copyright (C) 2010 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This is an inventory transactions list.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/formdata.inc.php");

function bucks($amount) {
  if ($amount != 0) return oeFormatMoney($amount);
  return '';
}

function esc4Export($str) {
  return str_replace('"', '\\"', $str);
}

function thisLineItem($row, $xfer=false) {
  global $grandtotal, $grandqty, $encount;

  $invnumber = '';
  $dpname = '';

  if (!empty($row['pid'])) {
    $ttype = xl('Sale');
    $dpname = $row['plname'];
    if (!empty($row['pfname'])) {
      $dpname .= ', ' . $row['pfname'];
      if (!empty($row['pmname'])) $dpname .= ' ' . $row['pmname'];
    }
    $invnumber = empty($row['invoice_refno']) ?
      "{$row['pid']}.{$row['encounter']}" : $row['invoice_refno'];
  }
  else if (!empty($row['distributor_id'])) {
    $ttype = xl('Distribution');
    if (!empty($row['organization'])) {
      $dpname = $row['organization'];
    }
    else {
      $dpname = $row['dlname'];
      if (!empty($row['dfname'])) {
        $dpname .= ', ' . $row['dfname'];
        if (!empty($row['dmname'])) $dpname .= ' ' . $row['dmname'];
      }
    }
  }
  else if (!empty($row['xfer_inventory_id']) || $xfer) {
    $ttype = xl('Transfer');
  }
  else if ($row['fee'] != 0) {
    $ttype = xl('Purchase');
  }
  else {
    $ttype = xl('Adjustment');
  }

  if ($_POST['form_csvexport']) {
    echo '"' . oeFormatShortDate($row['sale_date']) . '",';
    echo '"' . $ttype                               . '",';
    echo '"' . esc4Export($row['name'])             . '",';
    echo '"' . esc4Export($row['lot_number'])       . '",';
    // echo '"' . esc4Export($row['lot_number_2'])     . '",';
    echo '"' . esc4Export($row['warehouse'])        . '",';
    echo '"' . esc4Export($dpname)                  . '",';
    echo '"' . (0 - $row['quantity'])               . '",';
    echo '"' . bucks($row['fee'])                   . '",';
    echo '"' . $row['billed']                       . '",';
    echo '"' . esc4Export($row['notes'])            . '"' . "\n";
  }
  else {
    $bgcolor = (++$encount & 1) ? "#ddddff" : "#ffdddd";
?>

 <tr bgcolor="<?php echo $bgcolor; ?>">
  <td class="detail">
   <?php echo oeFormatShortDate($row['sale_date']); ?>
  </td>
  <td class="detail">
   <?php echo $ttype; ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($row['name']); ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($row['lot_number']); ?>
  </td>
  <!--
  <td class="detail">
   <?php echo htmlspecialchars($row['lot_number_2']); ?>
  </td>
  -->
  <td class="detail">
   <?php echo htmlspecialchars($row['warehouse']); ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($dpname); ?>
  </td>
  <td class="detail" align="right">
   <?php echo 0 - $row['quantity']; ?>
  </td>
  <td class="detail" align="right">
   <?php echo bucks($row['fee']); ?>
  </td>
  <td class="detail" align="center">
   <?php echo empty($row['billed']) ? '&nbsp;' : '*'; ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($row['notes']); ?>
  </td>
 </tr>
<?php
  } // End not csv export

  $grandtotal   += $row['fee'];
  $grandqty     -= $row['quantity'];

  // In the special case of a transfer, generate a second line item for
  // the source lot.
  if (!empty($row['xfer_inventory_id'])) {
    $row['xfer_inventory_id'] = 0;
    $row['lot_number'] = $row['lot_number_2'];
    $row['warehouse'] = $row['warehouse_2'];
    $row['quantity'] = 0 - $row['quantity'];
    $row['fee'] = 0 - $row['fee'];
    thisLineItem($row, true);
  }

} // end function

if (! acl_check('acct', 'rep')) die(xl("Unauthorized access."));

$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date   = fixDate($_POST['form_to_date']  , date('Y-m-d'));

$form_trans_type = isset($_POST['form_trans_type']) ? formData('form_trans_type') : '0';

// $form_facility  = $_POST['form_facility'];

$encount = 0;

if ($_POST['form_csvexport']) {
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Disposition: attachment; filename=inventory_transactions.csv");
  header("Content-Description: File Transfer");
  // CSV headers:
  echo '"' . xl('Date'       ) . '",';
  echo '"' . xl('Transaction') . '",';
  echo '"' . xl('Product'    ) . '",';
  echo '"' . xl('Lot'        ) . '",';
  // echo '"' . xl('Source'     ) . '",';
  echo '"' . xl('Warehouse'  ) . '",';
  echo '"' . xl('Who'        ) . '",';
  echo '"' . xl('Qty'        ) . '",';
  echo '"' . xl('Amount'     ) . '",';
  echo '"' . xl('Billed'     ) . '",';
  echo '"' . xl('Notes'      ) . '"' . "\n";
} // end export
else {
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Inventory Transactions','e') ?></title>
<style type="text/css">
 body       { font-family:sans-serif; font-size:10pt; font-weight:normal }
 .dehead    { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:bold }
 .detail    { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:normal }
</style>
</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
<center>

<h2><?php xl('Inventory Transactions','e')?></h2>

<form method='post' action='inventory_transactions.php'>

<table border='0' cellpadding='3'>

 <tr>
  <td>
<?php
  /*******************************************************************
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
  echo "   </select>\n&nbsp;";
  *******************************************************************/
?>
   <?php xl('Type','e'); ?>:
   <select name='form_trans_type' onchange='trans_type_changed()'>
<?php
foreach (array(
  '0' => xl('All'),
  '2' => xl('Purchase/Return'),
  '1' => xl('Sale'),
  '6' => xl('Distribution'),
  '4' => xl('Transfer'),
  '5' => xl('Adjustment'),
) as $key => $value)
{
  echo "<option value='$key'";
  if ($key == $form_trans_type) echo " selected";
  echo ">$value</option>\n";
}
?>
   </select>
   <?php xl('From','e'); ?>:
   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo $form_from_date ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>
   &nbsp;<?php xl('To','e'); ?>:
   <input type='text' name='form_to_date' id="form_to_date" size='10' value='<?php echo $form_to_date ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>
   &nbsp;
   <input type='submit' name='form_refresh' value="<?php xl('Refresh','e') ?>">
   &nbsp;
   <input type='submit' name='form_csvexport' value="<?php xl('Export to CSV','e') ?>">
   &nbsp;
   <input type='button' value='<?php xl('Print','e'); ?>' onclick='window.print()' />
  </td>
 </tr>

 <tr>
  <td height="1">
  </td>
 </tr>

</table>

<table border='0' cellpadding='1' cellspacing='2' width='98%'>
 <tr bgcolor="#dddddd">
  <td class="dehead">
   <?php xl('Date','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Transaction','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Product','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Lot','e'); ?>
  </td>
  <!--
  <td class="dehead">
   <?php xl('Source','e'); ?>
  </td>
  -->
  <td class="dehead">
   <?php xl('Warehouse','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Who','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Qty','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Amount','e'); ?>
  </td>
  <td class="dehead" align="Center">
   <?php xl('Billed','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Notes','e'); ?>
  </td>
 </tr>
<?php
} // end not export

if ($_POST['form_refresh'] || $_POST['form_csvexport']) {
  $from_date = $form_from_date;
  $to_date   = $form_to_date;

  $grandtotal = 0;
  $grandqty = 0;

  $query = "SELECT s.sale_date, s.fee, s.quantity, s.pid, s.encounter, " .
    "s.billed, s.notes, s.distributor_id, s.xfer_inventory_id, " .
    "p.fname AS pfname, p.mname AS pmname, p.lname AS plname, " .
    "u.fname AS dfname, u.mname AS dmname, u.lname AS dlname, u.organization, " .
    "d.name, fe.date, fe.invoice_refno, " .
    "i1.lot_number, i2.lot_number AS lot_number_2, " .
    "lo1.title AS warehouse, lo2.title AS warehouse_2 " .
    "FROM drug_sales AS s " .
    "JOIN drugs AS d ON d.drug_id = s.drug_id " .
    "LEFT JOIN drug_inventory AS i1 ON i1.inventory_id = s.inventory_id " .
    "LEFT JOIN drug_inventory AS i2 ON i2.inventory_id = s.xfer_inventory_id " .
    "LEFT JOIN patient_data AS p ON p.pid = s.pid " .
    "LEFT JOIN users AS u ON u.id = s.distributor_id " .
    "LEFT JOIN list_options AS lo1 ON lo1.list_id = 'warehouse' AND " .
    "lo1.option_id = i1.warehouse_id " .
    "LEFT JOIN list_options AS lo2 ON lo2.list_id = 'warehouse' AND " .
    "lo2.option_id = i2.warehouse_id " .
    "LEFT JOIN form_encounter AS fe ON fe.pid = s.pid AND fe.encounter = s.encounter " .
    "WHERE s.sale_date >= '$from_date' AND s.sale_date <= '$to_date' ";
  if ($form_trans_type == 2) { // purchase/return
    $query .= "AND s.pid = 0 AND s.distributor_id = 0 AND s.xfer_inventory_id = 0 AND s.fee != 0 ";
  }
  else if ($form_trans_type == 4) { // transfer
    $query .= "AND s.xfer_inventory_id != 0 ";
  }
  else if ($form_trans_type == 5) { // adjustment
    $query .= "AND s.pid = 0 AND s.distributor_id = 0 AND s.xfer_inventory_id = 0 AND s.fee = 0 ";
  }
  else if ($form_trans_type == 6) { // distribution
    $query .= "AND s.distributor_id != 0 ";
  }
  else if ($form_trans_type == 1) { // sale
    $query .= "AND s.pid != 0 ";
  }
  /*******************************************************************
  // If a facility was specified.
  if ($form_facility) {
    $query .= "AND fe.facility_id = '$form_facility' ";
  }
  *******************************************************************/
  $query .= "ORDER BY s.sale_date, s.sale_id";
  //
  $res = sqlStatement($query);
  while ($row = sqlFetchArray($res)) {
    thisLineItem($row);
  }

  if (!$_POST['form_csvexport']) {
?>

 <tr bgcolor="#dddddd">
  <td class="dehead" colspan="6">
   <?php xl('Grand Total','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php echo $grandqty; ?>
  </td>
  <td class="dehead" align="right">
   <?php echo bucks($grandtotal); ?>
  </td>
  <td class="dehead" colspan="2">

  </td>
 </tr>

<?php
  } // End not csv export
}

if (! $_POST['form_csvexport']) {
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
<?php
} // End not csv export
?>


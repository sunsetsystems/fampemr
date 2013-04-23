<?php
// Copyright (C) 2006-2013 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This is a report of sales by item description.  It's driven from
// SQL-Ledger so as to include all types of invoice items.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/sql-ledger.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");

function bucks($amount) {
  if ($amount) echo oeFormatMoney($amount);
}

function display_desc($desc) {
  if (preg_match('/^\S*?:(.+)$/', $desc, $matches)) {
    $desc = $matches[1];
  }
  return $desc;
}

// For a given encounter, this gets all charges and allocates payments and
// adjustments among them, if that has not already been done.
// Any invoice-level adjustments and payments are allocated among the line
// items in proportion to their line-level remaining balances.
//
function ensureLineAmounts($patient_id, $encounter_id) {
  global $aItems, $overpayments;

  $invno = "$patient_id.$encounter_id";
  if (isset($aItems[$invno])) return $invno;

  $adjusts = 0;  // sum of invoice level adjustments
  $payments = 0; // sum of invoice level payments
  $denom = 0;    // sum of adjusted line item charges
  $aItems[$invno] = array();

  // Get charges and copays from billing table.
  $tres = sqlStatement("SELECT b.code_type, b.code, b.fee " .
    "FROM billing AS b WHERE " .
    "b.pid = '$patient_id' AND b.encounter = '$encounter_id' AND " .
    "b.activity = 1 AND b.fee != 0");
  while ($trow = sqlFetchArray($tres)) {
    if ($trow['code_type'] == 'COPAY') {
      $payments -= $trow['fee'];
    }
    else {
      $codekey = $trow['code_type'] . ':' . $trow['code'];
      if (!isset($aItems[$invno][$codekey])) {
        // Charges, Adjustments, Payments
        $aItems[$invno][$codekey] = array(0, 0, 0);
      }
      $aItems[$invno][$codekey][0] += $trow['fee'];
      $denom += $trow['fee'];
    }
  }

  // Get charges from drug_sales table.
  $tres = sqlStatement("SELECT s.drug_id, s.fee " .
    "FROM drug_sales AS s WHERE " .
    "s.pid = '$patient_id' AND s.encounter = '$encounter_id' AND s.fee != 0");
  while ($trow = sqlFetchArray($tres)) {
    $codekey = 'PROD:' . $trow['drug_id'];
    if (!isset($aItems[$invno][$codekey])) {
      $aItems[$invno][$codekey] = array(0, 0, 0);
    }
    $aItems[$invno][$codekey][0] += $trow['fee'];
    $denom += $trow['fee'];
  }

  // Get adjustments and other payments from ar_activity table.
  $tres = sqlStatement("SELECT " .
    "a.code_type, a.code, a.adj_amount, a.pay_amount " .
    "FROM ar_activity AS a WHERE " .
    "a.pid = '$patient_id' AND a.encounter = '$encounter_id'");
  while ($trow = sqlFetchArray($tres)) {
    $codekey = $trow['code_type'] . ':' . $trow['code'];
    if (isset($aItems[$invno][$codekey])) {
      $aItems[$invno][$codekey][1] += $trow['adj_amount'];
      $aItems[$invno][$codekey][2] += $trow['pay_amount'];
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
        $factor = ($aItems[$invno][$codekey][0] - $aItems[$invno][$codekey][1] - $aItems[$invno][$codekey][2]) / $denom;
        $tmp = sprintf('%01.2f', $adjusts * $factor);
        $aItems[$invno][$codekey][1] += $tmp;
        $adjrem -= $tmp;
        $tmp = sprintf('%01.2f', $payments * $factor);
        $aItems[$invno][$codekey][2] += $tmp;
        $payrem -= $tmp;
      }
    }
    else {
      // Last line gets what's left to avoid rounding errors.
      $aItems[$invno][$codekey][1] += $adjrem;
      $aItems[$invno][$codekey][2] += $payrem;
    }
  }

  // For each line item having (payment > charge - adjustment), move the
  // overpayment amount to a global variable $overpayments.
  foreach ($aItems[$invno] AS $codekey => $dummy) {
    $diff = $aItems[$invno][$codekey][2] + $aItems[$invno][$codekey][1] - $aItems[$invno][$codekey][0];
    $diff = sprintf('%01.2f', $diff);
    if ($diff > 0.00) {
      $overpayments += $diff;
      $aItems[$invno][$codekey][2] -= $diff;
    }
  }

  return $invno;
}

function writeCatTotals($category, $catleft, $catqty, $cattotal, $catadjtotal, $catpaytotal) {
  if ($_POST['form_csvexport']) return;
  if (!$category) return;
  if ($catleft == '') $catleft = '&nbsp;';
?>
 <tr bgcolor="#ffdddd">
  <td class="detail">
   <?php echo $catleft; ?>
  </td>
  <td class="detail" colspan="3">
   <?php echo xl('Total for category') . ' '; echo display_desc($category); ?>
  </td>
  <td class="dehead" align="right">
   <?php echo $catqty ? $catqty : ''; ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($cattotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($catadjtotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($catpaytotal); ?>
  </td>
 </tr>
<?php
}

function writeProdTotals($category, $catleft, $product,
  $productqty, $producttotal, $prodadjtotal, $prodpaytotal)
{
  // Print product total.
  if ($_POST['form_csvexport']) {
    if (! $_POST['form_details']) {
      echo '"' . display_desc($category) . '",';
      echo '"' . display_desc($product)  . '",';
      echo '"' . $productqty             . '",';
      echo '"'; bucks($producttotal); echo '",';
      echo '"'; bucks($prodadjtotal); echo '",';
      echo '"'; bucks($prodpaytotal); echo '"';
      echo "\n";
    }
  }
  else {
?>
 <tr bgcolor="#ddddff">
  <td class="detail">
   <?php echo display_desc($catleft); $catleft = "&nbsp;"; ?>
  </td>
  <td class="detail" colspan="3">
   <?php if ($_POST['form_details']) echo xl('Total for') . ' '; echo display_desc($product); ?>
  </td>
  <td class="dehead" align="right">
   <?php echo $productqty; ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($producttotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($prodadjtotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($prodpaytotal); ?>
  </td>
 </tr>
<?php
  } // End not csv export
}

function thisLineItem($patient_id, $encounter_id, $code_type, $code, $rowcat,
  $description, $transdate, $qty, $amount, $irnumber='') {

  global $product, $category, $productleft, $catleft, $aItems;
  global $producttotal, $prodadjtotal, $prodpaytotal, $productqty;
  global $cattotal, $catadjtotal, $catpaytotal, $catqty;
  global $grandtotal, $grandadjtotal, $grandpaytotal, $grandqty;

  $invnumber = $irnumber ? $irnumber : "$patient_id.$encounter_id";
  $rowamount = sprintf('%01.2f', $amount);

  $invno = ensureLineAmounts($patient_id, $encounter_id);
  $codekey = $code_type . ':' . $code;
  $rowadj = $aItems[$invno][$codekey][1];
  $rowpay = $aItems[$invno][$codekey][2];

  if (empty($rowcat)) $rowcat = 'None';
  $rowproduct = $description;
  if (! $rowproduct) $rowproduct = 'Unknown';

  if ($product != $rowproduct || $category != $rowcat) {
    if ($product) {
      writeProdTotals($category, $catleft, $product, $productqty, $producttotal, $prodadjtotal, $prodpaytotal);
      $catleft = "&nbsp;";
    }
    $producttotal = 0;
    $prodadjtotal = 0;
    $prodpaytotal = 0;
    $productqty = 0;
    $product = $rowproduct;
    $productleft = $product;
  }

  if ($category != $rowcat) {
    writeCatTotals($category, '', $catqty, $cattotal, $catadjtotal, $catpaytotal);
    $cattotal = 0;
    $catadjtotal = 0;
    $catpaytotal = 0;
    $catqty = 0;
    $category = $rowcat;
    $catleft = $category;
  }

  if ($_POST['form_details']) {
    if ($_POST['form_csvexport']) {
      echo '"' . display_desc($category ) . '",';
      echo '"' . display_desc($product  ) . '",';
      echo '"' . oeFormatShortDate(display_desc($transdate)) . '",';
      echo '"' . display_desc($invnumber) . '",';
      echo '"' . display_desc($qty      ) . '",';
      echo '"'; bucks($rowamount); echo '", ';
      echo '"'; bucks($rowadj);    echo '", ';
      echo '"'; bucks($rowpay);    echo '"';
      echo "\n";
    }
    else {
?>

 <tr>
  <td class="detail">
   <?php echo display_desc($catleft); $catleft = "&nbsp;"; ?>
  </td>
  <td class="detail">
   <?php echo display_desc($productleft); $productleft = "&nbsp;"; ?>
  </td>
  <td class="detail">
   <?php echo oeFormatShortDate($transdate); ?>
  </td>
  <td class='delink' onclick='doinvopen(<?php echo "$patient_id,$encounter_id"; ?>)'>
   <?php echo $invnumber; ?>
  </td>
  <td class="detail" align="right">
   <?php echo $qty; ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($rowamount); ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($rowadj); ?>
  </td>
  <td class="detail" align="right">
   <?php bucks($rowpay); ?>
  </td>
 </tr>
<?php
    } // End not csv export
  } // end details
  $producttotal  += $rowamount;
  $prodadjtotal  += $rowadj;
  $prodpaytotal  += $rowpay;
  $cattotal      += $rowamount;
  $catadjtotal   += $rowadj;
  $catpaytotal   += $rowpay;
  $grandtotal    += $rowamount;
  $grandadjtotal += $rowadj;
  $grandpaytotal += $rowpay;
  $productqty    += $qty;
  $catqty        += $qty;
  $grandqty      += $qty;
} // end function thisLineItem

  if (! acl_check('acct', 'rep')) die(xl("Unauthorized access."));

  $form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
  $form_to_date   = fixDate($_POST['form_to_date']  , date('Y-m-d'));
  $form_facility  = $_POST['form_facility'];

  if ($_POST['form_csvexport']) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download; charset=utf-8");
    header("Content-Disposition: attachment; filename=sales_by_item.csv");
    header("Content-Description: File Transfer");
    // Prepend a BOM (Byte Order Mark) header to mark the data as UTF-8.  This is
    // said to work for Excel 2007 pl3 and up and perhaps also Excel 2003 pl3.  See:
    // http://stackoverflow.com/questions/155097/microsoft-excel-mangles-diacritics-in-csv-files
    // http://crashcoursing.blogspot.com/2011/05/exporting-csv-with-special-characters.html
    echo "\xEF\xBB\xBF";
    // CSV headers:
    if ($_POST['form_details']) {
      echo '"' . xl('Category') . '",';
      echo '"' . xl('Item'    ) . '",';
      echo '"' . xl('Date'    ) . '",';
      echo '"' . xl('Invoice' ) . '",';
      echo '"' . xl('Qty'     ) . '",';
      echo '"' . xl('Price'   ) . '",';
      echo '"' . xl('Adj'     ) . '",';
      echo '"' . xl('Payment' ) . '"';
      echo "\n";
    }
    else {
      echo '"' . xl('Category') . '",';
      echo '"' . xl('Item'    ) . '",';
      echo '"' . xl('Qty'     ) . '",';
      echo '"' . xl('Price'   ) . '",';
      echo '"' . xl('Adj'     ) . '",';
      echo '"' . xl('Payment' ) . '"';
      echo "\n";
    }
  } // end export
  else {
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Sales by Item','e') ?></title>

<style type="text/css">
 .dehead { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:bold }
 .detail { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:normal }
 .delink { color:#0000cc; font-family:sans-serif; font-size:10pt; font-weight:normal; cursor:pointer }
</style>

<script type="text/javascript" src="../../library/topdialog.js"></script>
<script type="text/javascript" src="../../library/dialog.js"></script>
<script language="JavaScript">
<?php require($GLOBALS['srcdir'] . "/restoreSession.php"); ?>
// Process click to pop up the add/edit window.
function doinvopen(ptid,encid) {
 dlgopen('../patient_file/pos_checkout.php?ptid=' + ptid + '&enc=' + encid, '_blank', 750, 550);
}
</script>

</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
<center>

<h2><?php xl('Sales by Item','e')?></h2>

<form method='post' action='sales_by_item.php'>

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
   &nbsp;<?php xl('From','e'); ?>:
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
   <input type='checkbox' name='form_details' value='1'<?php if ($_POST['form_details']) echo " checked"; ?>><?php xl('Details','e') ?>
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
   <?php xl('Category','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Item','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Date','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Invoice','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Qty','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Price','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Adj','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Payment','e'); ?>
  </td>
 </tr>
<?php
  } // end not export

  if ($_POST['form_refresh'] || $_POST['form_csvexport']) {
    $from_date = $form_from_date;
    $to_date   = $form_to_date;

    $category = "";
    $catleft = "";
    $cattotal = 0;
    $catadjtotal = 0;
    $catpaytotal = 0;
    $catqty = 0;
    $product = "";
    $productleft = "";
    $producttotal = 0;
    $prodadjtotal = 0;
    $prodpaytotal = 0;
    $productqty = 0;
    $grandtotal = 0;
    $grandadjtotal = 0;
    $grandpaytotal = 0;
    $grandqty = 0;
    $overpayments = 0;

    $aItems = array();

    $query = "SELECT b.fee, b.pid, b.encounter, b.code_type, b.code, b.units, " .
      "b.code_text, fe.date, fe.facility_id, fe.invoice_refno, lo.title " .
      "FROM billing AS b " .
      "JOIN code_types AS ct ON ct.ct_key = b.code_type " .
      "JOIN form_encounter AS fe ON fe.pid = b.pid AND fe.encounter = b.encounter " .
      "LEFT JOIN codes AS c ON c.code_type = ct.ct_id AND c.code = b.code AND c.modifier = b.modifier " .
      "LEFT JOIN list_options AS lo ON lo.list_id = 'superbill' AND lo.option_id = c.superbill " .
      "WHERE b.code_type != 'COPAY' AND b.activity = 1 AND b.fee != 0 AND " .
      "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";
    // If a facility was specified.
    if ($form_facility) {
      $query .= " AND fe.facility_id = '$form_facility'";
    }
    $query .= " ORDER BY lo.title, b.code, fe.date, fe.id";
    //
    $res = sqlStatement($query);
    while ($row = sqlFetchArray($res)) {
      thisLineItem($row['pid'], $row['encounter'], $row['code_type'], $row['code'],
        $row['title'], $row['code'] . ' ' . $row['code_text'],
        substr($row['date'], 0, 10), $row['units'], $row['fee'], $row['invoice_refno']);
    }
    //
    $query = "SELECT s.sale_date, s.fee, s.quantity, s.pid, s.encounter, " .
      "s.drug_id, d.name, fe.date, fe.facility_id, fe.invoice_refno " .
      "FROM drug_sales AS s " .
      "LEFT JOIN drugs AS d ON d.drug_id = s.drug_id " .
      "JOIN form_encounter AS fe ON " .
      "fe.pid = s.pid AND fe.encounter = s.encounter AND " .
      "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59' " .
      "WHERE s.fee != 0";
    // If a facility was specified.
    if ($form_facility) {
      $query .= " AND fe.facility_id = '$form_facility'";
    }
    $query .= " ORDER BY d.name, fe.date, fe.id";
    //
    $res = sqlStatement($query);
    while ($row = sqlFetchArray($res)) {
      thisLineItem($row['pid'], $row['encounter'], 'PROD', $row['drug_id'],
        xl('Products'), $row['name'], substr($row['date'], 0, 10), $row['quantity'],
        $row['fee'], $row['invoice_refno']);
    }

    // Write totals for last product.
    writeProdTotals($category, $catleft, $product, $productqty, $producttotal, $prodadjtotal, $prodpaytotal);

    // Write totals for last category.
    writeCatTotals($category, '', $catqty, $cattotal, $catadjtotal, $catpaytotal);

    // Write total for overpayments if there are any.
    if ($overpayments != 0.00) {
      writeCatTotals(xl('Overpayments'), xl('Overpayments'), 0, 0, 0, $overpayments);
      $grandpaytotal += $overpayments;
    }

    if (!$_POST['form_csvexport']) {
      // Write grand totals.
?>
 <tr bgcolor="#dddddd">
  <td class="detail" colspan="4">
   <?php xl('Grand Total','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php echo $grandqty; ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($grandtotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($grandadjtotal); ?>
  </td>
  <td class="dehead" align="right">
   <?php bucks($grandpaytotal); ?>
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

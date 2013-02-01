<?php
// Copyright (C) 2013 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This is a report of services by provider.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");

function display_desc($desc) {
  if (preg_match('/^\S*?:(.+)$/', $desc, $matches)) {
    $desc = $matches[1];
  }
  return $desc;
}

function thisLineItem($rowpatientid, $rowencounterid, $rowcodetype, $rowcode, $rowcat,
  $rowdesc, $rowqty, $rowirnumber='', $rowproviderid, $rowprovidername) {

  global $productname, $productcode, $producttotal, $invoices;
  global $providerid, $providername, $category, $provleft, $catleft;
  global $provtotal, $cattotal, $grandtotal;

  $invnumber = $rowirnumber ? $rowirnumber : "$rowpatientid.$rowencounterid";
  $codekey = $rowcodetype . ':' . $rowcode;

  if (empty($rowcat)) $rowcat = 'None';
  $rowproduct = $rowdesc;
  if (! $rowproduct) $rowproduct = 'Unknown';

  if ($productname != $rowproduct || $providerid != $rowproviderid) {
    if ($productname) {
      // Print product line.
      if ($_POST['form_csvexport']) {
        if (! $_POST['form_details']) {
          echo '"' . $providername . '",';
          echo '"' . $category . '",';
          echo '"' . $productcode . '",';
          echo '"' . $productname . '",';
          echo '"' . $producttotal . '",';
          echo '"' . $invoices . '"';
          echo "\n";
        }
      }
      else {
?>
 <tr bgcolor="#ddddff">
  <td class="detail">
   <?php echo $provleft; $provleft = "&nbsp;"; ?>
  </td>
  <td class="detail">
   <?php echo $catleft; $catleft = "&nbsp;"; ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($productcode); ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($productname); ?>
  </td>
  <td class="detail" align="right">
   <?php echo $producttotal; ?>
  </td>
  <td class="detail">
   <?php echo htmlspecialchars($invoices); ?>
  </td>
 </tr>
<?php
      } // End not csv export
    }
    $producttotal = 0;
    $productname = $rowproduct;
    $productcode = $rowcode;
    $invoices = '';
    if ($category != $rowcat || $providerid != $rowproviderid) {
      $category = $rowcat;
      $catleft = $category;
    }
  }

  if ($providerid != $rowproviderid) {
    if ($providerid) {
      // Print provider total.
      if (!$_POST['form_csvexport']) {
?>
 <tr bgcolor="#ffdddd">
  <td class="detail">
   &nbsp;
  </td>
  <td class="detail" colspan="3">
   <?php echo xl('Total for') . ' '; echo $providername; ?>
  </td>
  <td class="dehead" align="right">
   <?php echo $provtotal; ?>
  </td>
  <td class="dehead">
   &nbsp;
  </td>
 </tr>
<?php
      } // End not csv export
    }
    $providerid = $rowproviderid;
    $providername = $rowprovidername;
    $provleft = htmlspecialchars($providername);
    $provtotal = 0;
  }

  $producttotal  += $rowqty;
  $cattotal      += $rowqty;
  $provtotal     += $rowqty;
  $grandtotal    += $rowqty;

  if ($invoices !== '') $invoices .= ', ';
  $invoices .= $invnumber;

} // end function

if (! acl_check('acct', 'rep')) die(xl("Unauthorized access."));

$form_from_date = fixDate($_POST['form_from_date'], date('Y-m-d'));
$form_to_date   = fixDate($_POST['form_to_date']  , date('Y-m-d'));
$form_facility  = $_POST['form_facility'];

if ($_POST['form_csvexport']) {
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Disposition: attachment; filename=services_by_provider.csv");
  header("Content-Description: File Transfer");
  // CSV headers:
  echo '"Provider",';
  echo '"Category",';
  echo '"Code",';
  echo '"Description",';
  echo '"Units",';
  echo '"Invoices"';
  echo "\n";
} // end export
else {
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Services by Provider','e') ?></title>

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

<h2><?php xl('Services by Provider','e')?></h2>

<form method='post' action='services_by_provider.php'>

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
   <?php xl('Provider','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Category','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Code','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Description','e'); ?>
  </td>
  <td class="dehead" align="right">
   <?php xl('Units','e'); ?>
  </td>
  <td class="dehead">
   <?php xl('Invoices','e'); ?>
  </td>
 </tr>

<?php
} // end not export

if ($_POST['form_refresh'] || $_POST['form_csvexport']) {
  $from_date = $form_from_date;
  $to_date   = $form_to_date;

  $productname = '';
  $productcode = '';
  $producttotal = 0;
  $invoices = '';
  $category = '';
  $catleft = '';
  $cattotal = 0;
  $provider = '';
  $provleft = '';
  $provtotal = 0;
  $grandtotal = 0;

  $query = "SELECT b.pid, b.encounter, b.code_type, b.code, b.units, " .
    "b.code_text, fe.date, fe.facility_id, fe.invoice_refno, lo.title, " .
    "u.id, CONCAT(u.lname, ', ', u.fname, ' ', u.mname) AS providername " .
    "FROM billing AS b " .
    "JOIN code_types AS ct ON ct.ct_key = b.code_type " .
    "JOIN form_encounter AS fe ON fe.pid = b.pid AND fe.encounter = b.encounter " .
    "LEFT JOIN users AS u ON (b.provider_id = 0 AND fe.provider_id = u.id) OR (b.provider_id != 0 AND b.provider_id = u.id) " .
    "LEFT JOIN codes AS c ON c.code_type = ct.ct_id AND c.code = b.code AND c.modifier = b.modifier " .
    "LEFT JOIN list_options AS lo ON lo.list_id = 'superbill' AND lo.option_id = c.superbill " .
    "WHERE b.code_type != 'COPAY' AND b.activity = 1 AND " .
    "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";

  // If a facility was specified.
  if ($form_facility) {
    $query .= " AND fe.facility_id = '$form_facility'";
  }

  $query .= " ORDER BY u.lname, u.fname, u.mname, u.id, lo.title, b.code, fe.date, fe.id";

  $res = sqlStatement($query);

  while ($row = sqlFetchArray($res)) {
    thisLineItem($row['pid'], $row['encounter'], $row['code_type'], $row['code'],
      $row['title'], $row['code_text'],
      $row['units'], $row['invoice_refno'], $row['id'], $row['providername']);
  }

  // Generate totals line for last provider.
  thisLineItem(0, 0, '', '', '', '', 0 ,'', 0, '');
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

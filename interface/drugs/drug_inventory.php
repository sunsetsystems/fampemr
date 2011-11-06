<?php
 // Copyright (C) 2006-2010 Rod Roark <rod@sunsetsystems.com>
 //
 // This program is free software; you can redistribute it and/or
 // modify it under the terms of the GNU General Public License
 // as published by the Free Software Foundation; either version 2
 // of the License, or (at your option) any later version.

 require_once("../globals.php");
 require_once("$srcdir/acl.inc");
 require_once("drugs.inc.php");
 require_once("$srcdir/options.inc.php");
 require_once("$srcdir/formatting.inc.php");

 // Check authorization.
 $thisauth = acl_check('admin', 'drugs');
 if (!$thisauth) die(xl('Not authorized'));

 // get drugs
 $res = sqlStatement("SELECT d.*, " .
  "di.inventory_id, di.lot_number, di.expiration, di.manufacturer, " .
  "di.on_hand, lo.title " .
  "FROM drugs AS d " .
  "LEFT JOIN drug_inventory AS di ON di.drug_id = d.drug_id " .
  "AND di.destroy_date IS NULL " .
  "LEFT JOIN list_options AS lo ON lo.list_id = 'warehouse' AND " .
  "lo.option_id = di.warehouse_id " .
  "ORDER BY d.name, d.drug_id, di.expiration, di.lot_number");
?>
<html>

<head>
<?php html_header_show();?>

<link rel="stylesheet" href='<?php  echo $css_header ?>' type='text/css'>
<title><?php  xl('Drug Inventory','e'); ?></title>

<style>
tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; }
tr.detail { font-size:10pt; }
a, a:visited, a:hover { color:#0000cc; }
</style>

<script type="text/javascript" src="../../library/dialog.js"></script>

<script language="JavaScript">

// callback from add_edit_drug.php or add_edit_drug_inventory.php:
function refreshme() {
 location.reload();
}

// Process click on drug title.
function dodclick(id) {
 dlgopen('add_edit_drug.php?drug=' + id, '_blank', 725, 475);
}

// Process click on drug QOO or lot.
function doiclick(id, lot) {
 dlgopen('add_edit_lot.php?drug=' + id + '&lot=' + lot, '_blank', 600, 475);
}

</script>

</head>

<body class="body_top">
<form method='post' action='drug_inventory.php'>

<table width='100%' cellpadding='1' cellspacing='2'>
 <tr class='head'>
  <td title=<?php xl('Click to edit','e','\'','\''); ?>><?php  xl('Name','e'); ?></td>
  <td><?php  xl('Act','e'); ?></td>
  <td><?php  xl('NDC','e'); ?></td>
  <td><?php  xl('Form','e'); ?></td>
  <td><?php  xl('Size','e'); ?></td>
  <td><?php  xl('Unit','e'); ?></td>
  <td title=<?php xl('Click to receive (add) new lot','e','\'','\''); ?>><?php  xl('New','e'); ?></td>
  <td title=<?php xl('Click to edit','e','\'','\''); ?>><?php  xl('Lot','e'); ?></td>
  <td><?php  xl('Warehouse','e'); ?></td>
  <td><?php  xl('QOH','e'); ?></td>
  <td><?php  xl('Expires','e'); ?></td>
 </tr>
<?php 
 $lastid = "";
 $encount = 0;
 while ($row = sqlFetchArray($res)) {
  if ($lastid != $row['drug_id']) {
   ++$encount;
   $bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");
   $lastid = $row['drug_id'];
   echo " <tr class='detail' bgcolor='$bgcolor'>\n";
   echo "  <td onclick='dodclick($lastid)'>" .
    "<a href='' onclick='return false'>" .
    htmlentities($row['name']) . "</a></td>\n";
   echo "  <td>" . ($row['active'] ? xl('Yes') : xl('No')) . "</td>\n";
   echo "  <td>" . htmlentities($row['ndc_number']) . "</td>\n";
   echo "  <td>" . 
	generate_display_field(array('data_type'=>'1','list_id'=>'drug_form'), $row['form']) .
	"</td>\n";
   echo "  <td>" . $row['size'] . "</td>\n";
   echo "  <td>" .
	generate_display_field(array('data_type'=>'1','list_id'=>'drug_units'), $row['unit']) .
	"</td>\n";
   echo "  <td onclick='doiclick($lastid,0)' title='" . xl('Add new lot and transaction') . "'>" .
    "<a href='' onclick='return false'>" . xl('New') . "</a></td>\n";
  } else {
   echo " <tr class='detail' bgcolor='$bgcolor'>\n";
   echo "  <td colspan='7'>&nbsp;</td>\n";
  }
  if (!empty($row['inventory_id'])) {
   $lot_number = htmlentities($row['lot_number']);
   echo "  <td onclick='doiclick($lastid," . $row['inventory_id'] . ")'>" .
    "<a href='' onclick='return false'>$lot_number</a></td>\n";
   echo "  <td>" . $row['title'] . "</td>\n";
   echo "  <td>" . $row['on_hand'] . "</td>\n";
   echo "  <td>" . oeFormatShortDate($row['expiration']) . "</td>\n";
  } else {
   echo "  <td colspan='4'>&nbsp;</td>\n";
  }
  echo " </tr>\n";
 } // end while
?>
</table>

<center><p>
 <input type='button' value='<?php xl('Add Drug','e'); ?>' onclick='dodclick(0)' style='background-color:transparent' />
</p></center>

</form>
</body>
</html>

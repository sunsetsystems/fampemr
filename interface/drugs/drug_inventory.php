<?php
 // Copyright (C) 2006-2012 Rod Roark <rod@sunsetsystems.com>
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

// For each sorting option, specify the ORDER BY argument.
//
$ORDERHASH = array(
  'prod' => 'd.name, d.drug_id, di.expiration, di.lot_number',
  'ndc'  => 'd.ndc_number, d.name, d.drug_id, di.expiration, di.lot_number',
  'form' => 'lof.title, d.name, d.drug_id, di.expiration, di.lot_number',
  'lot'  => 'di.lot_number, d.name, d.drug_id, di.expiration',
  'wh'   => 'lo.title, d.name, d.drug_id, di.expiration, di.lot_number',
  'fac'  => 'f.name, d.name, d.drug_id, di.expiration, di.lot_number',
  'qoh'  => 'di.on_hand, d.name, d.drug_id, di.expiration, di.lot_number',
  'exp'  => 'di.expiration, d.name, d.drug_id, di.lot_number',
);

$form_facility = 0 + empty($_REQUEST['form_facility']) ? 0 : $_REQUEST['form_facility'];

// Incoming form_warehouse, if not empty is in the form "warehouse/facility".
// The facility part is an attribute used by JavaScript logic.
$form_warehouse = empty($_REQUEST['form_warehouse']) ? '' : $_REQUEST['form_warehouse'];
$tmp = explode('/', $form_warehouse);
$form_warehouse = $tmp[0];

$form_orderby = $ORDERHASH[$_REQUEST['form_orderby']] ? $_REQUEST['form_orderby'] : 'prod';
$orderby = $ORDERHASH[$form_orderby];

$where = "WHERE 1 = 1";
if ($form_facility ) $where .= " AND lo.option_value IS NOT NULL AND lo.option_value = '$form_facility'";
if ($form_warehouse) $where .= " AND di.warehouse_id IS NOT NULL AND di.warehouse_id = '$form_warehouse'";

 // get drugs
 $res = sqlStatement("SELECT d.*, " .
  "di.inventory_id, di.lot_number, di.expiration, di.manufacturer, " .
  "di.on_hand, lo.title, lo.option_value AS facid, f.name AS facname " .
  "FROM drugs AS d " .
  "LEFT JOIN drug_inventory AS di ON di.drug_id = d.drug_id " .
  "AND di.destroy_date IS NULL " .
  "LEFT JOIN list_options AS lo ON lo.list_id = 'warehouse' AND " .
  "lo.option_id = di.warehouse_id " .
  "LEFT JOIN facility AS f ON f.id = lo.option_value " .
  "LEFT JOIN list_options AS lof ON lof.list_id = 'drug_form' AND " .
  "lof.option_id = d.form " .
  "$where ORDER BY $orderby");
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

function dosort(orderby) {
 var f = document.forms[0];
 f.form_orderby.value = orderby;
 top.restoreSession();
 f.submit();
 return false;
}

// Enable/disable warehouse options depending on current facility.
function facchanged() {
 var f = document.forms[0];
 var facid = f.form_facility.value;
 var theopts = f.form_warehouse.options;
 for (var i = 1; i < theopts.length; ++i) {
  var tmp = theopts[i].value.split('/');
  var dis = facid && (tmp.length < 2 || tmp[1] != facid);
  theopts[i].disabled = dis;
  if (dis) theopts[i].selected = false;
 }
}

</script>

</head>

<body class="body_top">
<form method='post' action='drug_inventory.php' onsubmit='return top.restoreSession()'>

<table border='0' cellpadding='3' width='100%'>
 <tr>
  <td>
   <b><?php xl('Inventory Management','e') ?></b>
  </td>
  <td align='right'>
<?php
  // Build a drop-down list of facilities.
  //
  $query = "SELECT id, name FROM facility ORDER BY name";
  $fres = sqlStatement($query);
  echo "   <select name='form_facility' onchange='facchanged()'>\n";
  echo "    <option value=''>-- " . xl('All Facilities') . " --\n";
  while ($frow = sqlFetchArray($fres)) {
    $facid = $frow['id'];
    echo "    <option value='$facid'";
    if ($facid == $form_facility) echo " selected";
    echo ">" . $frow['name'] . "\n";
  }
  echo "   </select>\n";

  echo "&nbsp;";
  echo "   <select name='form_warehouse'>\n";
  echo "    <option value=''>" . xl('All Warehouses') . "</option>\n";
  $lres = sqlStatement("SELECT * FROM list_options " .
    "WHERE list_id = 'warehouse' ORDER BY seq, title");
  while ($lrow = sqlFetchArray($lres)) {
    echo "    <option value='" . $lrow['option_id'] . "/" . $lrow['option_value'] . "'";
    echo " id='fac" . $lrow['option_value'] . "'";
    if (strlen($form_warehouse)  > 0 && $lrow['option_id'] == $form_warehouse) {
      echo " selected";
    }
    echo ">" . xl_list_label($lrow['title']) . "</option>\n";
  }
  echo "   </select>\n";
?>
   &nbsp;
   <input type='submit' name='form_refresh' value="<?php xl('Refresh','e') ?>" />
  </td>
 </tr>
 <tr>
  <td height="1">
  </td>
 </tr>
</table>

<table width='100%' cellpadding='1' cellspacing='2'>
 <tr class='head'>
  <td title='<?php echo htmlspecialchars(xl('Click to edit'), ENT_QUOTES); ?>'>
   <a href="#" onclick="return dosort('prod')"
   <?php if ($form_orderby == "prod") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Name')); ?> </a>
  </td>
  <td>
   <?php echo htmlspecialchars(xl('Act')); ?>
  </td>
  <td>
   <a href="#" onclick="return dosort('ndc')"
   <?php if ($form_orderby == "ndc") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('NDC')); ?> </a>
  </td>
  <td>
   <a href="#" onclick="return dosort('form')"
   <?php if ($form_orderby == "form") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Form')); ?> </a>
  </td>
  <td>
   <?php echo htmlspecialchars(xl('Size')); ?>
  </td>
  <td>
   <?php echo htmlspecialchars(xl('Unit')); ?>
  </td>
  <td title='<?php echo htmlspecialchars(xl('Click to receive (add) new lot'), ENT_QUOTES); ?>'>
   <?php echo htmlspecialchars(xl('New')); ?>
  </td>
  <td title='<?php echo htmlspecialchars(xl('Click to edit'), ENT_QUOTES); ?>'>
   <a href="#" onclick="return dosort('lot')"
   <?php if ($form_orderby == "lot") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Lot')); ?> </a>
  </td>
  <td>
   <a href="#" onclick="return dosort('fac')"
   <?php if ($form_orderby == "fac") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Facility')); ?> </a>
  </td>
  <td>
   <a href="#" onclick="return dosort('wh')"
   <?php if ($form_orderby == "wh") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Warehouse')); ?> </a>
  </td>
  <td>
   <a href="#" onclick="return dosort('qoh')"
   <?php if ($form_orderby == "qoh") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('QOH')); ?> </a>
  </td>
  <td>
   <a href="#" onclick="return dosort('exp')"
   <?php if ($form_orderby == "exp") echo " style=\"color:#00cc00\""; ?>>
   <?php echo htmlspecialchars(xl('Expires')); ?> </a>
  </td>
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
   if ($row['dispensable']) {
    echo "  <td onclick='doiclick($lastid,0)' title='" . xl('Add new lot and transaction') . "'>" .
     "<a href='' onclick='return false'>" . xl('New') . "</a></td>\n";
   }
   else {
    echo "  <td title='" . xl('Inventory not enabled for this product') . "'>&nbsp;</td>\n";
   }
  } else {
   echo " <tr class='detail' bgcolor='$bgcolor'>\n";
   echo "  <td colspan='7'>&nbsp;</td>\n";
  }
  if (!empty($row['inventory_id'])) {
   $lot_number = htmlentities($row['lot_number']);
   echo "  <td onclick='doiclick($lastid," . $row['inventory_id'] . ")'>" .
    "<a href='' onclick='return false'>$lot_number</a></td>\n";
   echo "  <td>" . ($row['facid'] ? $row['facname'] : ('(' . xl('Unassigned') . ')')) . "</td>\n";
   echo "  <td>" . $row['title'] . "</td>\n";
   echo "  <td>" . $row['on_hand'] . "</td>\n";
   echo "  <td>" . oeFormatShortDate($row['expiration']) . "</td>\n";
  } else {
   echo "  <td colspan='5'>&nbsp;</td>\n";
  }
  echo " </tr>\n";
 } // end while
?>
</table>

<center><p>
 <input type='button' value='<?php echo htmlspecialchars(xl('Add Drug')); ?>' onclick='dodclick(0)' style='background-color:transparent' />
</p></center>

<input type="hidden" name="form_orderby" value="<?php echo $form_orderby ?>" />

</form>

<script language="JavaScript">
facchanged();
</script>

</body>
</html>

<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../../globals.php");
?>
<html>
<head>
<?php html_header_show();?>
<link rel="stylesheet" href="<?php echo $css_header; ?>" type="text/css">

<style type="text/css">
.rdeclass {
 background-color:#cc0000;
 color:#ffffff;
}

#navigation ul {
 background-color:transparent;
}
</style>

<script language="JavaScript">

function openNewFormURL(url, fullsize) {
 top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
 if (fullsize) { // if other frame is to be closed
  var istop = parent.window.name == 'RTop';
  parent.parent.left_nav.forceSpec(istop, !istop);
 }
 parent.location.href = url;
<?php } else { ?>
 top.frames['Main'].location.href = url;
<?php } ?>
}

function openNewForm(sel) {
 openNewFormURL(sel.options[sel.selectedIndex].value, false);
}

</script>

</head>
<body class="body_top">
<dl>
<?php //DYNAMIC FORM RETREIVAL
include_once("$srcdir/registry.inc");

function myGetRegistered($state="1", $limit="unlimited", $offset="0") {
  $sql = "SELECT category, nickname, name, state, directory, id, sql_run, " .
    "unpackaged, date, priority FROM registry WHERE " .
    "state LIKE \"$state\" ORDER BY category, priority";
  if ($limit != "unlimited") $sql .= " limit $limit, $offset";
  $res = sqlStatement($sql);
  if ($res) {
    for($iter=0; $row=sqlFetchArray($res); $iter++) {
      $all[$iter] = $row;
    }
  }
  else {
    return false;
  }
  return $all;
}

// usort comparison function for $reg table.
function cmp_forms($a, $b) {
  if ($a['category'] == $b['category']) {
    if ($a['priority'] == $b['priority']) {
      $name1 = $a['nickname'] ? $a['nickname'] : $a['name'];
      $name2 = $b['nickname'] ? $b['nickname'] : $b['name'];
      if ($name1 == $name2) return 0;
      return $name1 < $name2 ? -1 : 1;
    }
    return $a['priority'] < $b['priority'] ? -1 : 1;
  }
  return $a['category'] < $b['category'] ? -1 : 1;
}

$disabled = (empty($GLOBALS['gbl_rapid_workflow']) || $GLOBALS['gbl_rapid_workflow'] == 'fee_sheet') ? '' : 'disabled';

$reg = myGetRegistered();
$old_category = '';
echo "<FORM METHOD=POST NAME='choose'>\n";

// Merge any LBF entries into the table of forms.
// Note that the mapping value is used as the category name.
//
$lres = sqlStatement("SELECT * FROM list_options " .
  "WHERE list_id = 'lbfnames' ORDER BY mapping, seq, title");
if (sqlNumRows($lres)) {
  while ($lrow = sqlFetchArray($lres)) {
    $rrow = array();
    $rrow['category']  = $lrow['mapping'] ? $lrow['mapping'] : 'Layout Based';
    $rrow['name']      = $lrow['title'];
    $rrow['nickname']  = $lrow['title'];
    $rrow['directory'] = $lrow['option_id']; // should start with LBF
    $rrow['priority']  = $lrow['seq'];
    $reg[] = $rrow;
  }
}

// Sort by category.
usort($reg, 'cmp_forms');

if (!empty($reg)) {
  foreach ($reg as $entry) {
    $new_category = trim($entry['category']);
    $new_nickname = trim($entry['nickname']);
    if ($new_category == '') $new_category = 'miscellaneous';
    $nickname = $new_nickname ? $new_nickname : $entry['name'];
    if ($old_category != $new_category) {
      $ns_category = str_replace(' ', '_', $new_category);
      if ($old_category) echo "</select>\n";
      echo "<select name=" . $ns_category . " onchange='openNewForm(this)' $disabled>\n";
      echo " <option value=" . $ns_category . ">" . $new_category . "</option>\n";
      $old_category = $new_category;
    }
    echo " <option value='" . $rootdir .
      '/patient_file/encounter/load_form.php?formname=' .
      urlencode($entry['directory']) . "'>" . xl_form_title($nickname) . "</option>\n";
  }
  echo "</select>\n";
}

if (!empty($GLOBALS['gbl_rapid_workflow'])) {
  $rdeform = $GLOBALS['gbl_rapid_workflow'];
  $frow = sqlQuery("SELECT form_id AS id FROM forms WHERE pid = '$pid' AND " .
    "encounter = '$encounter' AND formdir = '$rdeform' AND deleted = 0 " .
    "ORDER BY id LIMIT 1");
  $url = "$rootdir/patient_file/encounter/";
  if (empty($frow['id'])) {
    $url .= "load_form.php?formname=$rdeform";
  }
  else {
    $url .= "view_form.php?formname=$rdeform&id=" . $frow['id'];
  }
  $label = ($rdeform == 'fee_sheet') ? xl('Fee Sheet') : xl('Rapid Data Entry');
  echo "<p><input type='button' value='$label' " .
    "onclick='openNewFormURL(\"$url\", true)' class='rdeclass' /></p>\n";
}

echo "</FORM>\n";
?>
</dl>

</body>
</html>

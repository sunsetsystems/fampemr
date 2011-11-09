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

function openNewFormURL(url) {
 top.restoreSession();
<?php if ($GLOBALS['concurrent_layout']) { ?>
  parent.location.href = url;
<?php } else { ?>
  top.frames['Main'].location.href = url;
<?php } ?>
}

function openNewForm(sel) {
 openNewFormURL(sel.options[sel.selectedIndex].value);
}

</script>

</head>
<body class="body_top">
<dl>
<?php //DYNAMIC FORM RETREIVAL
include_once("$srcdir/registry.inc");

function myGetRegistered($state="1", $limit="unlimited", $offset="0") {
  $sql = "SELECT category, nickname, name, state, directory, id, sql_run, " .
    "unpackaged, date FROM registry WHERE " .
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

$disabled = empty($GLOBALS['gbl_rapid_workflow']) ? '' : 'disabled';

$reg = myGetRegistered();
$old_category = '';
echo "<FORM METHOD=POST NAME='choose'>\n";
if (!empty($reg)) {
  foreach ($reg as $entry) {
	  $new_category = trim($entry['category']);
	  $new_nickname = trim($entry['nickname']);
	  if ($new_category == '') {$new_category = 'miscellaneous';}
	  if ($new_nickname != '') {$nickname = $new_nickname;}
	  else {$nickname = $entry['name'];}
	  if ($old_category != $new_category) {
		  $new_category_ = $new_category;
		  $new_category_ = str_replace(' ','_',$new_category_);
		  if ($old_category != '') {echo "</select>\n";}
		  echo "<select name=" . $new_category_ . " onchange='openNewForm(this)' $disabled>\n";
		  echo " <option value=" . $new_category_ . ">" . $new_category . "</option>\n";
		  $old_category = $new_category;
	  }
	  echo " <option value='" . $rootdir .
		  '/patient_file/encounter/load_form.php?formname=' .
		  urlencode($entry['directory']) . "'>" . xl_form_title($nickname) . "</option>\n";
  }
  echo "</select>\n";
}

// This shows Layout Based Form names just like the above.
//
$lres = sqlStatement("SELECT * FROM list_options " .
  "WHERE list_id = 'lbfnames' ORDER BY seq, title");
if (sqlNumRows($lres)) {
  echo "<select name='lbfnames' onchange='openNewForm(this)' $disabled>\n";
  echo "<option value='lbfnames'>" . xl('Layout Based') . "</option>\n";
  while ($lrow = sqlFetchArray($lres)) {
    $option_id = $lrow['option_id']; // should start with LBF
    $title = $lrow['title'];
	  echo "<option value='$rootdir/patient_file/encounter/load_form.php?" .
      "formname=$option_id'>$title</option>\n";
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
  echo "<p><input type='button' value='" . xl('Rapid Data Entry') . "' " .
    "onclick='openNewFormURL(\"$url\")' class='rdeclass' /></p>\n";
}

echo "</FORM>\n";
?>
</dl>

</body>
</html>

<?php
// Copyright (C) 2011 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../globals.php");
require_once("$srcdir/patient.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");
require_once("../../custom/code_types.inc.php");

// This is the "related code" type that identifies reportable services and
// products, and serves to group them.
$RELCODETYPE = 'REPORT';

function formatBucks($amount) {
  if ($amount) return sprintf("%.2f", $amount);
  return '';
}

function formatInt($amount) {
  if ($amount) return sprintf("%d", $amount);
  return '';
}

function formatText($desc) {
  return $desc;
}

// Get the related code for reporting.
function getRelatedCode($rcfield) {
  global $RELCODETYPE;
  if (!empty($rcfield)) {
    $relcodes = explode(';', $rcfield);
    foreach ($relcodes as $codestring) {
      if ($codestring === '') continue;
      list($codetype, $code) = explode(':', $codestring);
      if ($codetype !== $RELCODETYPE) continue;
      return $code;
    }
  }
  return '';
}

function getRelatedCodeText($rcfield) {
  global $RELCODETYPE, $code_types;
  $rc = getRelatedCode($rcfield);
  $tmp = sqlQuery("SELECT code_text FROM codes WHERE " .
    "code_type = '" . $code_types[$RELCODETYPE]['id'] . "' AND " .
    "code = '$rc' AND active = 1");
  return $tmp['code_text'];
}

// Compute age in years given a DOB and "as of" date.
//
function getAge($dob, $asof='') {
  if (empty($asof)) $asof = date('Y-m-d');
  $a1 = explode('-', substr($dob , 0, 10));
  $a2 = explode('-', substr($asof, 0, 10));
  $age = $a2[0] - $a1[0];
  if ($a2[1] < $a1[1] || ($a2[1] == $a1[1] && $a2[2] < $a1[2])) --$age;
  return $age;
}

// Function to write a report line.
//
function thisLineItem($dispgroup, $code, $arr, $bgcolor='', $colspan=1) {
  if ($_POST['form_csvexport']) {
    echo '"' . formatText($dispgroup) . '",';
    echo '"' . formatText($code)      . '",';
    echo '"' . formatInt($arr[0])      . '",';
    echo '"' . formatBucks($arr[1])    . '",';
    echo '"' . formatInt($arr[2])      . '",';
    echo '"' . formatInt($arr[3])      . '",';
    echo '"' . formatInt($arr[4])      . '",';
    echo '"' . formatInt($arr[5])      . '",';
    echo '"' . formatInt($arr[6])      . '",';
    echo '"' . formatInt($arr[7])      . '"' . "\n";
  }
  else {
?>

 <tr<?php if ($bgcolor) echo " bgcolor='$bgcolor'"; ?>>
  <td class="detail"<?php if ($colspan > 1) echo " colspan='$colspan'"; ?>>
   <?php echo formatText($dispgroup); ?>
  </td>
<?php if ($colspan < 2) { ?>
  <td class="detail">
   <?php echo formatText($code); ?>
  </td>
<?php } ?>
  <td class="detail" align="right">
   <?php echo formatInt($arr[0]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatBucks($arr[1]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[2]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[3]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[4]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[5]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[6]); ?>
  </td>
  <td class="detail" align="right">
   <?php echo formatInt($arr[7]); ?>
  </td>
 </tr>
<?
  } // End not csv export
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
  header("Content-Disposition: attachment; filename=famp_services_products_visits.csv");
  header("Content-Description: File Transfer");
  // CSV headers:
  echo '"Group",';
  echo '"Item",';
  echo '"Count",';
  echo '"Charges",';
  echo '"New Male",';
  echo '"New Female",';
  echo '"Existing Male",';
  echo '"Existing Female",';
  echo '"Under 25",';
  echo '"25+"' . "\n";
}
else { // not export
?>
<html>
<head>
<?php html_header_show();?>
<title><?php xl('Family Planning Services and Products','e') ?></title>
</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>
<center>

<h2><?php xl('Family Planning Services and Products','e')?></h2>

<form method='post' action='famp_services_products_visits.php'>

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
   &nbsp;<?php xl('From','e') ?>:
   <input type='text' name='form_from_date' id="form_from_date" size='10' value='<?php echo $form_from_date ?>'
    onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='yyyy-mm-dd'>
   <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
    id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
    title='<?php xl('Click here to choose a date','e'); ?>'>
   &nbsp;<?php xl('To','e') ?>:
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
   <?xl('Group','e')?>
  </td>
  <td class="dehead">
   <?xl('Item','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('Count','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('Charges','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('New Male','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('New Female','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('Existing Male','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('Existing Female','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('Under 25','e')?>
  </td>
  <td class="dehead" align="right">
   <?xl('25+','e')?>
  </td>
 </tr>
<?php
} // end not export

// If generating a report.
//
if ($_POST['form_refresh'] || $_POST['form_csvexport']) {
  $from_date = $form_from_date;
  $to_date   = $form_to_date;

  $grid = array();

  $query = "SELECT b.pid, b.encounter, b.code_type, b.code, b.units, " .
    "b.fee, b.code_text, " .
    "c.related_code, fe.pc_catid, pd.DOB, pd.sex " .
    "FROM billing AS b " .
    "JOIN code_types AS ct ON ct.ct_key = b.code_type " .
    "JOIN codes AS c ON c.code_type = ct.ct_id AND c.code = b.code AND " .
    "c.modifier = b.modifier AND c.related_code LIKE '%$RELCODETYPE:%' " .
    "JOIN form_encounter AS fe ON fe.pid = b.pid AND fe.encounter = b.encounter " .
    "JOIN patient_data AS pd ON pd.pid = b.pid " .
    "WHERE b.activity = 1 AND b.code_type != 'COPAY' AND " .
    "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";
  // If a facility was specified.
  if ($form_facility) {
    $query .= " AND fe.facility_id = '$form_facility'";
  }
  $query .= " ORDER BY b.code, b.pid, b.encounter";
  //
  $res = sqlStatement($query);
  while ($row = sqlFetchArray($res)) {
    $rctext = getRelatedCodeText($row['related_code']);

    $key = "$rctext|" . $row['code_type'] . "|" . $row['code_text'];

    if (!isset($grid[$key])) {
      $grid[$key] = array(0,0,0,0,0,0,0,0);
    }
    $grid[$key][0] += $row['units'] ? $row['units'] : 1;
    $grid[$key][1] += $row['fee'];

    $male = strtolower(substr($row['sex'], 0, 1)) == 'm';

    // Assuming here that a given key occurs no more than once per visit.
    // Might want to add some testing here if that is a problem.

    // '10' is hard-coded to mean new client.  Will need to fix this.
    if ($row['pc_catid'] == '10') {
      if ($male) ++$grid[$key][2]; // new male
      else       ++$grid[$key][3]; // new female
    }
    else {
      if ($male) ++$grid[$key][4]; // existing male
      else       ++$grid[$key][5]; // existing female
    }

    if (getAge($row['DOB']) < 25)
      ++$grid[$key][6]; // under 25
    else
      ++$grid[$key][7]; // 25+
  }

  $query = "SELECT s.quantity, s.fee, s.pid, s.encounter, " .
    "d.name, d.related_code, fe.pc_catid, pd.DOB, pd.sex " .
    "FROM drug_sales AS s " .
    "JOIN drugs AS d ON d.drug_id = s.drug_id AND d.active = 1 " .
    "JOIN form_encounter AS fe ON " .
    "fe.pid = s.pid AND fe.encounter = s.encounter " .
    "JOIN patient_data AS pd ON pd.pid = s.pid " .
    "WHERE d.related_code LIKE '%$RELCODETYPE:%' AND " .
    "fe.date >= '$from_date 00:00:00' AND fe.date <= '$to_date 23:59:59'";
  // If a facility was specified.
  if ($form_facility) {
    $query .= " AND fe.facility_id = '$form_facility'";
  }
  $query .= " ORDER BY d.name, s.pid, s.encounter";
  //
  $res = sqlStatement($query);
  while ($row = sqlFetchArray($res)) {
    $rctext = getRelatedCodeText($row['related_code']);

    $key = "$rctext|*|" . $row['name'];

    if (!isset($grid[$key])) {
      $grid[$key] = array(0,0,0,0,0,0,0,0);
    }
    $grid[$key][0] += $row['quantity'] ? $row['quantity'] : 1;
    $grid[$key][1] += $row['fee'];

    $male = strtolower(substr($row['sex'], 0, 1)) == 'm';

    // Assuming here that a given key occurs no more than once per visit.
    // Might want to add some testing here if that is a problem.

    // '10' is hard-coded to mean new client.  Will need to fix this.
    if ($row['pc_catid'] == '10') {
      if ($male) ++$grid[$key][2]; // new male
      else       ++$grid[$key][3]; // new female
    }
    else {
      if ($male) ++$grid[$key][4]; // existing male
      else       ++$grid[$key][5]; // existing female
    }

    if (getAge($row['DOB']) < 25)
      ++$grid[$key][6]; // under 25
    else
      ++$grid[$key][7]; // 25+
  }

  ksort($grid);

  $grandtot = array(0,0,0,0,0,0,0,0);
  $grouptot = array(0,0,0,0,0,0,0,0);
  $lastgroup = "";
  $dispgroup = "";

  foreach ($grid as $key => $arr) {
    list($group, $codetype, $code) = explode('|', $key);
    if ($group != $lastgroup) {
      if ($lastgroup !== '') {
        thisLineItem("Total for $lastgroup", "", $grouptot, "#dddddd", 2);
      }
      $grouptot = array(0,0,0,0,0,0,0,0);
      $dispgroup = $lastgroup = $group;
    }
    thisLineItem($dispgroup, $code, $arr, "#ddddff");
    $dispgroup = '';
    for ($i = 0; $i < count($grouptot); ++$i) {
      $grouptot[$i] += $arr[$i];
      $grandtot[$i] += $arr[$i];
    }
  }

  if (!$_POST['form_csvexport']) {
    if ($lastgroup !== '') {
      thisLineItem("Total for $lastgroup", "", $grouptot, "#dddddd", 2);
    }
    thisLineItem("Total", "", $grandtot, "#ffdddd", 2);
  }
} // end report generation

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

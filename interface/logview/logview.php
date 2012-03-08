<?php

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../globals.php");
require_once("$srcdir/log.inc");
require_once("$srcdir/formdata.inc.php");
require_once("$srcdir/formatting.inc.php");

if ($_REQUEST['form_csvexport']) {
  header("Pragma: public");
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Content-Type: application/force-download");
  header("Content-Disposition: attachment; filename=logview.csv");
  header("Content-Description: File Transfer");
} // end export
else {
?>
<html>
<head>
<?php html_header_show(); ?>
<link rel="stylesheet" href='<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css' type='text/css'>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.2.2.min.js"></script>
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<style>
#logview {
    width: 100%;
}
#logview table {
    width:100%;
    border-collapse: collapse;
}
#logview th {
    background-color: #cccccc;
    cursor: pointer; cursor: hand;
    padding: 5px 5px;
    align: left;
    text-align: left;
}
#logview td {
    background-color: #ffffff;
    border-bottom: 1px solid #808080;
    cursor: default;
    padding: 5px 5px;
    vertical-align: top;
}
.highlight {
    background-color: #336699;
    color: #336699;
}
</style>
</head>
<body class="body_top">
<font class="title"><?php  xl('Logs Viewer','e'); ?></font>
<br>
<?php
} // end not export

$err_message = 0;

if ($_GET["start_date"])
  $start_date = formData('start_date','G');

if ($_GET["end_date"])
  $end_date = formData('end_date','G');

/*
 * Start date should not be greater than end date - Date Validation
 */
if ($start_date && $end_date)
{
	if($start_date > $end_date) {
    if (!$_REQUEST['form_csvexport']) {
		  echo "<table><tr class='alert'><td colspan=7>"; xl('Start Date should not be greater than End Date',e);
		  echo "</td></tr></table>"; 
    }
		$err_message=1;	
	}
}

$form_user = formData('form_user','R');

$res = sqlStatement("select distinct LEFT(date,10) as date from log order by date desc limit 30");
for($iter = 0; $row = sqlFetchArray($res); $iter++) {
  $ret[$iter] = $row;
}

// Get the users list.
$sqlQuery = "SELECT username, fname, lname FROM users " .
  "WHERE active = 1 AND ( info IS NULL OR info NOT LIKE '%Inactive%' ) ";

$ures = sqlStatement($sqlQuery);

$get_sdate = $start_date ? $start_date : date("Y-m-d");
$get_edate = $end_date ? $end_date : date("Y-m-d");
$sortby = formData('sortby', 'G');

if (!$_REQUEST['form_csvexport']) {
?>
<br>
<FORM METHOD="GET" name="theform" id="theform">
<input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
<table>
<tr><td>
<span class="text"><?php  xl('Start Date','e'); ?>: </span>
</td><td>
<input type="text" size="10" name="start_date" id="start_date" value="<?php echo $start_date ? substr($start_date, 0, 10) : date('Y-m-d'); ?>" title="<?php  xl('yyyy-mm-dd Date of service','e'); ?>" onkeyup="datekeyup(this,mypcc)" onblur="dateblur(this,mypcc)" />
<img src="../pic/show_calendar.gif" align="absbottom" width="24" height="22" id="img_begin_date" border="0" alt="[?]" style="cursor: pointer; cursor: hand" title="<?php  xl('Click here to choose a date','e'); ?>">&nbsp;
</td>
<td>
<span class="text"><?php  xl('End Date','e'); ?>: </span>
</td><td>
<input type="text" size="10" name="end_date" id="end_date" value="<?php echo $end_date ? substr($end_date, 0, 10) : date('Y-m-d'); ?>" title="<?php  xl('yyyy-mm-dd Date of service','e'); ?>" onkeyup="datekeyup(this,mypcc)" onblur="dateblur(this,mypcc)" />
<img src="../pic/show_calendar.gif" align="absbottom" width="24" height="22" id="img_end_date" border="0" alt="[?]" style="cursor: pointer; cursor: hand" title="<?php  xl('Click here to choose a date','e'); ?>">&nbsp;
</td>
<td>
<!-- not used -->
</td>
<td>
<!-- not used -->
</td>
</tr>
<tr><td>
<span class='text'><?php  xl('User','e'); ?>: </span>
</td>
<td>
<?php
echo "<select name='form_user'>\n";
echo " <option value=''>" . xl('All') . "</option>\n";
while ($urow = sqlFetchArray($ures)) {
  if (!trim($urow['username'])) continue;
  echo " <option value='" . $urow['username'] . "'";
  if ($urow['username'] == $form_user) echo " selected";
  echo ">" . $urow['lname'];
  if ($urow['fname']) echo ", " . $urow['fname'];
  echo "</option>\n";
}
echo "</select>\n";
?>
</td>
<td>
<!-- list of events name -->
<span class='text'><?php  xl('Name of Events','e'); ?>: </span>
</td>
<td>
<?php 
$res = sqlStatement("select distinct event from log order by event ASC");
$ename_list = array(); $j=0;
while ($erow = sqlFetchArray($res)) {
  if (!trim($erow['event'])) continue;
  $data = explode('-', $erow['event']);
  $data_c = count($data);
  $ename = $data[0];
  for($i = 1; $i < ($data_c - 1); $i++) {
    $ename .= "-" . $data[$i];
  }
  $ename_list[$j] = $ename;
  $j = $j + 1;
}

$ename_list=array_unique($ename_list);
$ename_list=array_merge($ename_list);
$ecount=count($ename_list);
echo "<select name='eventname'>\n";
echo " <option value=''>" . xl('All') . "</option>\n";
for($k=0;$k<$ecount;$k++) {
echo " <option value='" .$ename_list[$k]. "'";
  if ($ename_list[$k] == $eventname && $ename_list[$k]!= "") echo " selected";
  echo ">" . $ename_list[$k];
  echo "</option>\n";
}
echo "</select>\n"; 
?>
</td>
<td>
<!-- Not used --></td>
</td>
<td>
<!-- Not used --></td>
<tr>
<td>
<!-- Not used -->
</td>
<td>
<input type='submit' name='form_refresh' value="<?php xl('Refresh','e') ?>" />
</td>
<td>
<!-- Not used -->
</td>
<td>
<input type='submit' name='form_csvexport' value="<?php xl('Export to CSV','e') ?>" />
</td>
</tr>
</table>
</FORM>

<?php
} // end not export

if ($start_date && $end_date && $err_message != 1) {
  if ($_REQUEST['form_csvexport']) {
    // CSV headers:
    echo '"' . xl('Date'    ) . '",';
    echo '"' . xl('Event'   ) . '",';
    echo '"' . xl('User'    ) . '",';
    if (empty($GLOBALS['disable_non_default_groups'])) {
      echo '"' . xl('Group'   ) . '",';
    }
    echo '"' . xl('Comments') . '"' . "\n";
  }
  else { // not export
?>
<div id="logview">
<table>
 <tr>
  <th id="sortby_date" class="text" title="<?php xl('Sort by date/time','e'); ?>"><?php xl('Date','e'); ?></th>
  <th id="sortby_event" class="text" title="<?php xl('Sort by Event','e'); ?>"><?php  xl('Event','e'); ?></th>
  <th id="sortby_user" class="text" title="<?php xl('Sort by User','e'); ?>"><?php  xl('User','e'); ?></th>
<?php if (empty($GLOBALS['disable_non_default_groups'])) { ?>
  <th id="sortby_group" class="text" title="<?php xl('Sort by Group','e'); ?>"><?php  xl('Group','e'); ?></th>
<?php } ?>
  <th id="sortby_comments" class="text" title="<?php xl('Sort by Comments','e'); ?>"><?php  xl('Comments','e'); ?></th>
 </tr>
<?php
  } // end not export

$eventname = formData('eventname','G');

if ($ret = getEvents(array('sdate' => $get_sdate,'edate' => $get_edate, 'user' => $form_user, 'sortby' => $_GET['sortby'], 'levent' =>$eventname))) {
  foreach ($ret as $iter) {
    //translate comments
    $patterns = array ('/^success/','/^failure/','/ encounter/');
    $replace = array (xl('success'), xl('failure'), xl('encounter','',' '));
    $trans_comments = preg_replace($patterns, $replace, $iter["comments"]);
    if ($_REQUEST['form_csvexport']) {
      echo '"' . oeFormatShortDate(substr($iter["date"], 0, 10)) . substr($iter["date"], 10) . '",';
      echo '"' . xl($iter["event"]    ) . '",';
      echo '"' . xl($iter["user"]     ) . '",';
      if (empty($GLOBALS['disable_non_default_groups'])) {
        echo '"' . xl($iter["groupname"]) . '",';
      }
      echo '"' . $trans_comments        . '"' . "\n";
    }
    else { // not export
?>
 <TR class="oneresult">
  <TD class="text"><?php echo oeFormatShortDate(substr($iter["date"], 0, 10)) . substr($iter["date"], 10) ?></TD>
  <TD class="text"><?php echo xl($iter["event"])?></TD>
  <TD class="text"><?php echo $iter["user"]?></TD>
<?php if (empty($GLOBALS['disable_non_default_groups'])) { ?>
  <TD class="text"><?php echo $iter["groupname"]?></TD>
<?php } ?>
  <TD class="text"><?php echo $trans_comments?></TD>
 </TR>
<?php
    } // end not export
  }
}

if (!$_REQUEST['form_csvexport']) {
?>
</table>
</div>

<?php
} // end not export
} // end query results display

if (!$_REQUEST['form_csvexport']) {
?>

</body>

<script language="javascript">

// jQuery stuff to make the page a little easier to use
$(document).ready(function(){
    // funny thing here... good learning experience
    // the TR has TD children which have their own background and text color
    // toggling the TR color doesn't change the TD color
    // so we need to change all the TR's children (the TD's) just as we did the TR
    // thus we have two calls to toggleClass:
    // 1 - for the parent (the TR)
    // 2 - for each of the children (the TDs)
    $(".oneresult").mouseover(function() { $(this).toggleClass("highlight"); $(this).children().toggleClass("highlight"); });
    $(".oneresult").mouseout(function() { $(this).toggleClass("highlight"); $(this).children().toggleClass("highlight"); });

    // click-able column headers to sort the list
    $("#sortby_date").click(function() { $("#sortby").val("date"); $("#theform").submit(); });
    $("#sortby_event").click(function() { $("#sortby").val("event"); $("#theform").submit(); });
    $("#sortby_user").click(function() { $("#sortby").val("user"); $("#theform").submit(); });
<?php if (empty($GLOBALS['disable_non_default_groups'])) { ?>
    $("#sortby_group").click(function() { $("#sortby").val("groupname"); $("#theform").submit(); });
<?php } ?>
    $("#sortby_comments").click(function() { $("#sortby").val("comments"); $("#theform").submit(); });
});

/* required for popup calendar */
Calendar.setup({inputField:"start_date", ifFormat:"%Y-%m-%d", button:"img_begin_date"});
Calendar.setup({inputField:"end_date", ifFormat:"%Y-%m-%d", button:"img_end_date"});

</script>

</html>
<?php
} // end not export
?>

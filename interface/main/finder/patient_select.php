<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

include_once("../../globals.php");
include_once("$srcdir/patient.inc");
include_once("$srcdir/formdata.inc.php");

$fstart = $_REQUEST['fstart'] + 0;
$popup  = empty($_REQUEST['popup']) ? 0 : 1;
$message = strip_escape_custom($_GET['message']);
?>

<html>
<head>
<?php html_header_show();?>

<link rel=stylesheet href="<?php echo $css_header;?>" type="text/css">
<style>
form {
    padding: 0px;
    margin: 0px;
}
#searchCriteria {
    text-align: center;
    width: 100%;
    font-size: 0.8em;
    background-color: #ddddff;
    font-weight: bold;
    padding: 3px;
}
#searchResultsHeader { 
    width: 100%;
    background-color: lightgrey;
}
#searchResultsHeader table { 
    width: 96%;  /* not 100% because the 'searchResults' table has a scrollbar */
    border-collapse: collapse;
}
#searchResultsHeader th {
    font-size: 0.7em;
}
#searchResults {
    width: 100%;
    height: 80%;
    overflow: auto;
}

.srName { width: 12%; }
.srPhone { width: 11%; }
.srSS { width: 11%; }
.srDOB { width: 8%; }
.srID { width: 7%; }
.srPID { width: 7%; }
.srNumEnc { width: 11%; }
.srNumDays { width: 11%; }
.srDateLast { width: 11%; }
.srDateNext { width: 11%; }
.srMisc { width: 10%; }

#searchResults table {
    width: 100%;
    border-collapse: collapse;
    background-color: white;
}
#searchResults tr {
    cursor: hand;
    cursor: pointer;
}
#searchResults td {
    font-size: 0.7em;
    border-bottom: 1px solid #eee;
}
.oneResult { }
.billing { color: red; font-weight: bold; }
.highlight { 
    background-color: #336699;
    color: white;
}
</style>

<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.2.2.min.js"></script>

<script language="JavaScript">

// This is called when forward or backward paging is done.
//
function submitList(offset) {
 var f = document.forms[0];
 var i = parseInt(f.fstart.value) + offset;
 if (i < 0) i = 0;
 f.fstart.value = i;
 f.submit();
}

</script>

</head>
<body class="body_top">

<form method='post' action='patient_select.php' name='theform'>
<input type='hidden' name='fstart'  value='<?php echo $fstart  ?>' />

<?php
$MAXSHOW = 100; // maximum number of results to display at once

//the maximum number of patient records to display:
$sqllimit = $MAXSHOW;
$given = "*, DATE_FORMAT(DOB,'%m/%d/%Y') as DOB_TS";
$orderby = "lname ASC, fname ASC";

if ($popup) {
  echo "<input type='hidden' name='popup' value='1' />\n";

  // Construct WHERE clause and save search parameters as form fields.
  $where = "1 = 1";
  $fres = sqlStatement("SELECT * FROM layout_options " .
    "WHERE form_id = 'DEM' AND uor > 0 AND field_id != '' " .
    "ORDER BY group_name, seq");
  while ($frow = sqlFetchArray($fres)) {
    $field_id  = $frow['field_id'];
    if (strpos($field_id, 'em_') === 0) continue;
    $data_type = $frow['data_type'];
    if (!empty($_REQUEST[$field_id])) {
      $value = formData($field_id,"R",true);
      if ($field_id == 'pid') {
        $where .= " AND $field_id = '$value'";
      }
      else if ($field_id == 'pubpid') {
        $where .= " AND $field_id LIKE '$value'";
      }
      else {
        $where .= " AND $field_id LIKE '$value%'";
      }
      echo "<input type='hidden' name='$field_id' value='$value' />\n";
    }
  }

  $sql = "SELECT $given FROM patient_data " .
    "WHERE $where ORDER BY $orderby LIMIT $fstart, $sqllimit";
  $rez = sqlStatement($sql);
  $result = array();
  while ($row = sqlFetchArray($rez)) $result[] = $row;
  _set_patient_inc_count($sqllimit, count($result), $where);
}
else {
  $patient = formData("patient","R");
  $findBy  = $_REQUEST['findBy'];
  $searchFields = $_REQUEST['searchFields'];

  echo "<input type='hidden' name='patient' value='$patient' />\n";
  echo "<input type='hidden' name='findBy'  value='$findBy' />\n";

  if ($findBy == "Last")
      $result = getPatientLnames("$patient", $given, $orderby, $sqllimit, $fstart);
  else if ($findBy == "ID")
      $result = getPatientId("$patient", $given, "id ASC, ".$orderby, $sqllimit, $fstart);
  else if ($findBy == "DOB")
      $result = getPatientDOB("$patient", $given, "DOB ASC, ".$orderby, $sqllimit, $fstart);
  else if ($findBy == "SSN")
      $result = getPatientSSN("$patient", $given, "ss ASC, ".$orderby, $sqllimit, $fstart);
  elseif ($findBy == "Phone")                  //(CHEMED) Search by phone number
      $result = getPatientPhone("$patient", $given, $orderby, $sqllimit, $fstart);
  else if ($findBy == "Any")
      $result = getByPatientDemographics("$patient", $given, $orderby, $sqllimit, $fstart);
  else if ($findBy == "Filter") {
      $result = getByPatientDemographicsFilter($searchFields, "$patient", $given, $orderby, $sqllimit, $fstart);
  }
}
?>

</form>

<table border='0' cellpadding='5' cellspacing='0' width='100%'>
 <tr>
  <td class='text'>
   <a href="./patient_select_help.php" target=_new>[<?php xl('Help','e'); ?>]&nbsp</a>
  </td>
  <td class='text' align='center'>
<?php if ($message) echo "<font color='red'><b>$message</b></font>\n"; ?>
  </td>
  <td class='text' align='right'>
<?php
// Show start and end row number, and number of rows, with paging links.
//
// $count = $fstart + $GLOBALS['PATIENT_INC_COUNT']; // Why did I do that???
$count = $GLOBALS['PATIENT_INC_COUNT'];
$fend = $fstart + $MAXSHOW;
if ($fend > $count) $fend = $count;
?>
<?php if ($fstart) { ?>
   <a href="javascript:submitList(-<?php echo $MAXSHOW ?>)">
    &lt;&lt;
   </a>
   &nbsp;&nbsp;
<?php } ?>
   <?php echo ($fstart + 1) . " - $fend of $count" ?>
<?php if ($count > $fend) { ?>
   &nbsp;&nbsp;
   <a href="javascript:submitList(<?php echo $MAXSHOW ?>)">
    &gt;&gt;
   </a>
<?php } ?>
  </td>
 </tr>
</table>

<div id="searchResultsHeader">
<table>
<tr>
<th class="srName"><?php xl('Name','e');?></th>
<th class="srPhone"><?php xl('Phone','e');?></th>
<th class="srSS"><?php xl('SS','e');?></th>
<th class="srDOB"><?php xl('DOB','e');?></th>
<th class="srID"><?php xl('ID','e');?></th>

<?php if (empty($GLOBALS['patient_search_results_style'])) { ?>
<th class="srPID"><?php xl('PID','e');?></th>
<th class="srNumEnc"><?php xl('[Number Of Encounters]','e');?></th>
<th class="srNumDays"><?php xl('[Days Since Last Encounter]','e');?></th>
<th class="srDateLast"><?php xl('[Date of Last Encounter]','e');?></th>
<th class="srDateNext">
<?php
$add_days = 90;
if (!$popup && preg_match('/^(\d+)\s*(.*)/',$patient,$matches) > 0) {
  $add_days = $matches[1];
  $patient = $matches[2];
}
?>
[<?php echo $add_days?> <?php xl('Days From Last Encounter','e'); ?>]
</th>

<?php
}
else {
  // Alternate patient search results style; this gets address plus other
  // fields that are mandatory, up to a limit of 5.
  $extracols = array();
  $tres = sqlStatement("SELECT field_id, title FROM layout_options " .
    "WHERE form_id = 'DEM' AND ( uor > 1 AND field_id != '' " .
    "OR uor > 0 AND field_id = 'street' ) AND " .
    "field_id NOT LIKE '_name' AND " .
    "field_id NOT LIKE 'phone%' AND " .
    "field_id NOT LIKE 'title' AND " .
    "field_id NOT LIKE 'ss' AND " .
    "field_id NOT LIKE 'DOB' AND " .
    "field_id NOT LIKE 'pubpid' " .
    "ORDER BY group_name, seq LIMIT 5");
  while ($trow = sqlFetchArray($tres)) {
    $extracols[$trow['field_id']] = $trow['title'];
    echo "<th class='srMisc'>" . xl($trow['title']) . "</th>\n";
  }
}
?>

</tr>
</table>
</div>

<div id="searchResults">

<table>
<tr>
<?php
if ($result) {
    foreach ($result as $iter) {
        echo "<tr class='oneresult' id='".$iter['pid']."'>";
        echo  "<td class='srName'>" . $iter['lname'] . ", " . $iter['fname'] . "</td>\n";
        //other phone number display setup for tooltip
        $phone_biz = '';
        if ($iter{"phone_biz"} != "") {
            $phone_biz = " [business phone ".$iter{"phone_biz"}."] ";
        }
        $phone_contact = '';
        if ($iter{"phone_contact"} != "") {
            $phone_contact = " [contact phone ".$iter{"phone_contact"}."] ";
        }
        $phone_cell = '';
        if ($iter{"phone_cell"} != "") {
            $phone_cell = " [cell phone ".$iter{"phone_cell"}."] ";
        }
        $all_other_phones = $phone_biz.$phone_contact.$phone_cell;
        if ($all_other_phones == '') {$all_other_phones = 'No other phone numbers listed';}
        //end of phone number display setup, now display the phone number(s)
        echo "<td class='srPhone' title='$all_other_phones'>" . $iter['phone_home']. "</td>\n";
        
        echo "<td class='srSS'>" . $iter['ss'] . "</td>";
        if ($iter{"DOB"} != "0000-00-00 00:00:00") {
            echo "<td class='srDOB'>" . $iter['DOB_TS'] . "</td>";
        } else {
            echo "<td class='srDOB'>&nbsp;</td>";
        }
        
        echo "<td class='srID'>" . $iter['pubpid'] . "</td>";

        if (empty($GLOBALS['patient_search_results_style'])) {

          echo "<td class='srPID'>" . $iter['pid'] . "</td>";
          
          //setup for display of encounter date info
          $encounter_count = 0;
          $day_diff = ''; 
          $last_date_seen = ''; 
          $next_appt_date= ''; 
          $pid = '';

          // calculate date differences based on date of last encounter with billing entries
          $query = "select DATE_FORMAT(max(form_encounter.date),'%m/%d/%y') as mydate," .
                  " (to_days(current_date())-to_days(max(form_encounter.date))) as day_diff," .
                  " DATE_FORMAT(max(form_encounter.date) + interval " . $add_days .
                  " day,'%m/%d/%y') as next_appt, dayname(max(form_encounter.date) + interval " .
                  $add_days." day) as next_appt_day from form_encounter " .
                  "join billing on billing.encounter = form_encounter.encounter and " .
                  "billing.pid = form_encounter.pid and billing.activity = 1 and " .
                  "billing.code_type not like 'COPAY' where ".
                  "form_encounter.pid = " . $iter{"pid"};
          $statement= sqlStatement($query);
          if ($results = mysql_fetch_array($statement, MYSQL_ASSOC)) {
              $last_date_seen = $results['mydate']; 
              $day_diff = $results['day_diff'];
              $next_appt_date= $results['next_appt_day'].', '.$results['next_appt'];
          }
          // calculate date differences based on date of last encounter regardless of billing
          $query = "select DATE_FORMAT(max(form_encounter.date),'%m/%d/%y') as mydate," .
                  " (to_days(current_date())-to_days(max(form_encounter.date))) as day_diff," .
                  " DATE_FORMAT(max(form_encounter.date) + interval " . $add_days .
                  " day,'%m/%d/%y') as next_appt, dayname(max(form_encounter.date) + interval " .
                  $add_days." day) as next_appt_day from form_encounter " .
                  " where form_encounter.pid = " . $iter{"pid"};
          $statement= sqlStatement($query);
          if ($results = mysql_fetch_array($statement, MYSQL_ASSOC)) {
              $last_date_seen = $results['mydate']; 
              $day_diff = $results['day_diff'];
              $next_appt_date= $results['next_appt_day'].', '.$results['next_appt'];
          }

          //calculate count of encounters by distinct billing dates with cpt4
          //entries
          $query = "select count(distinct date) as encounter_count " .
                   " from billing ".
                   " where code_type not like 'COPAY' and activity = 1 " .
                   " and pid = ".$iter{"pid"};
          $statement= sqlStatement($query);
          if ($results = mysql_fetch_array($statement, MYSQL_ASSOC)) {
              $encounter_count_billed = $results['encounter_count'];
          }
          // calculate count of encounters, regardless of billing
          $query = "select count(date) as encounter_count ".
                      " from form_encounter where ".
                      " pid = ".$iter{"pid"};
          $statement= sqlStatement($query);
          if ($results = mysql_fetch_array($statement, MYSQL_ASSOC)) {
              $encounter_count = $results['encounter_count'];
          }
          echo "<td class='srNumEnc'>" . $encounter_count . "</td>\n";
          echo "<td class='srNumDay'>" . $day_diff . "</td>\n";
          echo "<td class='srDateLast'>" . $last_date_seen . "</td>\n";
          echo "<td class='srDateNext'>" . $next_appt_date . "</td>\n";
        }

        else { // alternate search results style
          foreach ($extracols as $field_id => $title) {
            echo "<td class='srMisc'>" . $iter[$field_id] . "</td>\n";
          }
        }
    }
}
?>
</table>
</div>  <!-- end searchResults DIV -->

<script language="javascript">

// jQuery stuff to make the page a little easier to use

$(document).ready(function(){
    // $("#searchparm").focus();
    $(".oneresult").mouseover(function() { $(this).addClass("highlight"); });
    $(".oneresult").mouseout(function() { $(this).removeClass("highlight"); });
    $(".oneresult").click(function() { SelectPatient(this); });
    // $(".event").dblclick(function() { EditEvent(this); });
});

var SelectPatient = function (eObj) {
<?php 
// For the old layout we load a frameset that also sets up the new pid.
// The new layout loads just the demographics frame here, which in turn
// will set the pid and load all the other frames.
if ($GLOBALS['concurrent_layout']) {
    $newPage = "../../patient_file/summary/demographics.php?set_pid=";
    $target = "document";
}
else {
    $newPage = "../../patient_file/patient_file.php?set_pid=";
    $target = "top";
}
?>
    objID = eObj.id;
    var parts = objID.split("~");
    <?php if ($popup) echo "opener."; echo $target; ?>.location.href = '<?php echo $newPage; ?>' + parts[0];
    <?php if ($popup) echo "window.close();\n"; ?>
    return true;
}

</script>

</body>
</html>


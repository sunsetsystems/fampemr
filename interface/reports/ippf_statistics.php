<?php
// Copyright (C) 2008-2012 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This module creates statistical reports related to family planning
// and sexual and reproductive health.

include_once("../globals.php");
include_once("../../library/patient.inc");
include_once("../../library/acl.inc");

$alertmsg = '';

// Might want something different here.
//
if (! acl_check('acct', 'rep')) die("Unauthorized access.");

$report_type = empty($_GET['t']) ? 'i' : $_GET['t'];

$from_date     = fixDate($_POST['form_from_date']);
$to_date       = fixDate($_POST['form_to_date'], date('Y-m-d'));
$form_by_arr   = $_POST['form_by'];     // this is an array
$form_show     = $_POST['form_show'];   // this is an array
$form_facility = isset($_POST['form_facility']) ? $_POST['form_facility'] : '';
$form_sexes    = isset($_POST['form_sexes']) ? $_POST['form_sexes'] : '3';
$form_content  = isset($_POST['form_content']) ? $_POST['form_content'] : '1';
$form_output   = isset($_POST['form_output']) ? 0 + $_POST['form_output'] : 1;

// One of these is chosen as the left column, or Y-axis, of the report.
//
if ($report_type == 'm') {
  $report_title = xl('Member Association Statistics Report');
  $arr_by = array(
    101 => xl('MA Category'),
    102 => xl('Specific Service'),
    // 6   => xl('Contraceptive Method'),
    // 104 => xl('Specific Contraceptive Service');
    17  => xl('Clients who had a visit'),
    9   => xl('Outbound Internal Referrals'),
    10  => xl('Outbound External Referrals'),
    14  => xl('Inbound Internal Referrals'),
    15  => xl('Inbound External Referrals'),
    103 => xl('Referral Source'),
    2   => xl('Total'),
  );
  $arr_content = array(
    1 => xl('Services'),
    2 => xl('Unique Clients'),
    4 => xl('Unique New Clients'),
    // 5 => xl('Contraceptive Products'),
  );
  $arr_report = array(
    // Items are content|row|column|column|...
    /*****************************************************************
    '2|2|3|4|5|8|11' => xl('Client Profile - Unique Clients'),
    '4|2|3|4|5|8|11' => xl('Client Profile - New Clients'),
    *****************************************************************/
  );
}
else if ($report_type == 'g') {
  $report_title = xl('GCAC Statistics Report');
  $arr_by = array(
    13 => xl('Abortion-Related Categories'),
    1  => xl('Total SRH & Family Planning'),
    12 => xl('Pre-Abortion Counseling'),
    5  => xl('Abortion Method'), // includes surgical and drug-induced
    8  => xl('Post-Abortion Followup'),
    7  => xl('Post-Abortion Contraception'),
    11 => xl('Complications of Abortion'),
    10  => xl('Outbound External Referrals'),
    20  => xl('External Referral Followups'),
  );
  $arr_content = array(
    1 => xl('Services'),
    2 => xl('Unique Clients'),
    4 => xl('Unique New Clients'),
  );
  $arr_report = array(
    /*****************************************************************
    '1|11|13' => xl('Complications by Service Provider'),
    *****************************************************************/
  );
}
else {
  $report_title = xl('IPPF Statistics Report');
  $arr_by = array(
    3  => xl('General Service Category'),
    4  => xl('Specific Service'),
    104 => xl('Specific Contraceptive Service'),
    6  => xl('Contraceptive Method'),
    9   => xl('Outbound Internal Referrals'),
    10  => xl('Outbound External Referrals'),
    14  => xl('Inbound Internal Referrals'),
    15  => xl('Inbound External Referrals'),
  );
  $arr_content = array(
    1 => xl('Services'),
    3 => xl('New Acceptors'),
    5 => xl('Contraceptive Products'),
  );
  $arr_report = array(
  );
}

if (empty($form_by_arr)) {
  $tmp = array_keys($arr_by);
  $form_by_arr = array($tmp[0]);
}
if (empty($form_show)) $form_show = array('1');

// Compute age in years given a DOB and "as of" date.
//
function getAge($dob, $asof='') {
  if (empty($asof)) $asof = date('Y-m-d');
  $a1 = explode('-', substr($dob , 0, 10));
  $a2 = explode('-', substr($asof, 0, 10));
  $age = $a2[0] - $a1[0];
  if ($a2[1] < $a1[1] || ($a2[1] == $a1[1] && $a2[2] < $a1[2])) --$age;
  // echo "<!-- $dob $asof $age -->\n"; // debugging
  return $age;
}

$cellcount = 0;

function genStartRow($att) {
  global $cellcount, $form_output;
  if ($form_output != 3) echo " <tr $att>\n";
  $cellcount = 0;
}

function genEndRow() {
  global $form_output;
  if ($form_output == 3) {
    echo "\n";
  }
  else {
    echo " </tr>\n";
  }
}

function getListTitle($list, $option) {
  $row = sqlQuery("SELECT title FROM list_options WHERE " .
    "list_id = '$list' AND option_id = '$option'");
  if (empty($row['title'])) return $option;
  return xl_list_label($row['title']);
}

// Usually this generates one cell, but allows for two or more.
//
function genAnyCell($data, $align='left', $class='', $colspan=1) {
  global $cellcount, $form_output;
  if (!is_array($data)) {
    $data = array(0 => $data);
  }
  foreach ($data as $datum) {
    if ($form_output == 3) {
      if ($cellcount) echo ',';
      echo '"' . $datum . '"';
    }
    else {
      echo "  <td";
      if ($class) echo " class='$class'";
      if ($colspan > 1) echo " colspan='$colspan'";
      if ($align) echo " align='$align'";
      echo ">$datum</td>\n";
    }
    ++$cellcount;
  }
}

function genHeadCell($data, $align='left', $colspan=1) {
  genAnyCell($data, $align, 'dehead', $colspan);
}

// Create an HTML table cell containing a numeric value, and track totals.
//
function genNumCell($num, $cnum) {
  global $atotals, $asubtotals, $form_output;
  $atotals[$cnum] += $num;
  $asubtotals[$cnum] += $num;
  if (empty($num) && $form_output != 3) $num = '&nbsp;';
  genAnyCell($num, 'right', 'detail');
}

// Translate an IPPF code to the corresponding descriptive name of its
// contraceptive method, or to an empty string if none applies.
//
function getContraceptiveMethod($code) {
  global $contra_group_name;
  $contra_group_name = '';
  $key = '';
  /*******************************************************************
  if (preg_match('/^111101/', $code)) {
    $key = xl('Pills');
  }
  else if (preg_match('/^11111[1-9]/', $code)) {
    $key = xl('Injectables');
  }
  else if (preg_match('/^11112[1-9]/', $code)) {
    $key = xl('Implants');
  }
  else if (preg_match('/^111132/', $code)) {
    $key = xl('Patch');
  }
  else if (preg_match('/^111133/', $code)) {
    $key = xl('Vaginal Ring');
  }
  else if (preg_match('/^112141/', $code)) {
    $key = xl('Male Condoms');
  }
  else if (preg_match('/^112142/', $code)) {
    $key = xl('Female Condoms');
  }
  else if (preg_match('/^11215[1-9]/', $code)) {
    $key = xl('Diaphragms/Caps');
  }
  else if (preg_match('/^11216[1-9]/', $code)) {
    $key = xl('Spermicides');
  }
  else if (preg_match('/^11317[1-9]/', $code)) {
    $key = xl('IUD');
  }
  else if (preg_match('/^121181.13/', $code)) {
    $key = xl('Female VSC');
  }
  else if (preg_match('/^122182.13/', $code)) {
    $key = xl('Male VSC');
  }
  *******************************************************************/
  // Normalize contraception codes to the table values.
  if (preg_match('/^11/', $code)) {
    $code = substr($code, 0, 6) . '110';
    if ($code == '112152110') $code = '112152010';
  }
  else if (preg_match('/^12/', $code)) {
    $code = substr($code, 0, 7) . '13';
  }
  $row = sqlQuery("SELECT title, mapping FROM list_options WHERE " .
    "list_id = 'ippfconmeth' AND option_id = '$code'");
  if (!empty($row['title'])) {
    $key = $row['title'];
    if (!empty($row['mapping'])) {
      $contra_group_name = $row['mapping'];
    }
  }
  /******************************************************************/
  else if (preg_match('/^145212/', $code)) {
    $key = xl('Emergency Contraception');
  }
  else if (preg_match('/^131191.10/', $code)) {
    $key = xl('Awareness-Based');
  }
  return $key;
}

// Helper function to find a contraception-related IPPF code from
// the related_code element of the given array.
//
function getRelatedContraceptiveCode($row) {
  if (!empty($row['related_code'])) {
    $relcodes = explode(';', $row['related_code']);
    foreach ($relcodes as $codestring) {
      if ($codestring === '') continue;
      list($codetype, $code) = explode(':', $codestring);
      if ($codetype !== 'IPPF') continue;
      // Check if the related code concerns contraception.
      $tmp = getContraceptiveMethod($code);
      if (!empty($tmp)) return $code;
    }
  }
  return '';
}

// Helper function to find an abortion-method IPPF code from
// the related_code element of the given array.
//
function getRelatedAbortionMethod($row) {
  if (!empty($row['related_code'])) {
    $relcodes = explode(';', $row['related_code']);
    foreach ($relcodes as $codestring) {
      if ($codestring === '') continue;
      list($codetype, $code) = explode(':', $codestring);
      if ($codetype !== 'IPPF') continue;
      // Check if the related code concerns contraception.
      $tmp = getAbortionMethod($code);
      if (!empty($tmp)) return $code;
    }
  }
  return '';
}

// Translate an IPPF code to the corresponding descriptive name of its
// abortion method, or to an empty string if none applies.
//
function getAbortionMethod($code) {
  $key = '';
  if (preg_match('/^25222[34]/', $code)) {
    if (preg_match('/^2522231/', $code)) {
      $key = xl('D&C');
    }
    else if (preg_match('/^2522232/', $code)) {
      $key = xl('D&E');
    }
    else if (preg_match('/^2522233/', $code)) {
      $key = xl('MVA');
    }
    else if (preg_match('/^252224/', $code)) {
      $key = xl('Medical');
    }
    else {
      $key = xl('Other Surgical');
    }
  }
  return $key;
}

// Determine if a recent gcac service was performed.
//
function hadRecentAbService($pid, $encdate, $includeIncomplete=false) {
  $query = "SELECT COUNT(*) AS count " .
    "FROM form_encounter AS fe, billing AS b, codes AS c WHERE " .
    "fe.pid = '$pid' AND " .
    "fe.date <= '$encdate' AND " .
    "DATE_ADD(fe.date, INTERVAL 14 DAY) > '$encdate' AND " .
    "b.pid = fe.pid AND " .
    "b.encounter = fe.encounter AND " .
    "b.activity = 1 AND " .
    "b.code_type = 'MA' AND " .
    "c.code_type = '12' AND " .
    "c.code = b.code AND c.modifier = b.modifier AND " .
    "( c.related_code LIKE '%IPPF:252223%' OR c.related_code LIKE '%IPPF:252224%'";
  if ($includeIncomplete) {
    // In this case we want to include treatment for incomplete.
    $query .= " OR c.related_code LIKE '%IPPF:252225%'";
  }
  $query .= " )";
  $tmp = sqlQuery($query);
  return !empty($tmp['count']);
}

// Get the "client status" as descriptive text.
//
function getGcacClientStatus($row) {
  $pid = $row['pid'];
  $encdate = $row['encdate'];

  if (hadRecentAbService($pid, $encdate))
    return xl('MA Client Accepting Abortion');

  // Check for a GCAC visit form.
  // This will the most recent GCAC visit form for visits within
  // the past 2 weeks, although there really should be such a form
  // attached to the visit associated with $row.
  $query = "SELECT lo.title " .
    "FROM forms AS f, form_encounter AS fe, lbf_data AS d, list_options AS lo " .
    "WHERE f.pid = '$pid' AND " .
    "f.formdir = 'LBFgcac' AND " .
    "f.deleted = 0 AND " .
    "fe.pid = f.pid AND fe.encounter = f.encounter AND " .
    "fe.date <= '$encdate' AND " .
    "DATE_ADD(fe.date, INTERVAL 14 DAY) > '$encdate' AND " .
    "d.form_id = f.form_id AND " .
    "d.field_id = 'client_status' AND " .
    "lo.list_id = 'clientstatus' AND " .
    "lo.option_id = d.field_value " .
    "ORDER BY d.form_id DESC LIMIT 1";
  $irow = sqlQuery($query);
  if (!empty($irow['title'])) return xl_list_label($irow['title']);

  // Check for a referred-out abortion.
  $query = "SELECT COUNT(*) AS count " .
    "FROM transactions AS t " .
    "LEFT JOIN codes AS c ON t.refer_related_code LIKE 'REF:%' AND " .
    "c.code_type = '16' AND " .
    "c.code = SUBSTRING(t.refer_related_code, 5) " .
    "WHERE " .
    "t.title = 'Referral' AND " .
    "t.refer_external < '4' AND " .
    "t.refer_date IS NOT NULL AND " .
    "t.refer_date <= '$encdate' AND " .
    "DATE_ADD(t.refer_date, INTERVAL 14 DAY) > '$encdate' AND " .
    "( t.refer_related_code LIKE '%IPPF:252223%' OR " .
    "t.refer_related_code LIKE '%IPPF:252224%' OR " .
    "( c.related_code IS NOT NULL AND " .
    "( c.related_code LIKE '%IPPF:252223%' OR " .
    "c.related_code LIKE '%IPPF:252224%' )))";

  $tmp = sqlQuery($query);
  if (!empty($tmp['count'])) return xl('Outbound Referral');

  return xl('Indeterminate');
}

// Helper function called after the reporting key is determined for a row.
//
function loadColumnData($key, $row, $quantity=1) {
  global $areport, $arr_titles, $form_content, $from_date, $to_date, $arr_show;

  // If we are counting new acceptors, then this must be a report of contraceptive
  // methods and a contraceptive start date is provided.
  if ($form_content == '3') {
    if (empty($row['contrastart'])) return;
  }

  // If we are counting new clients, then require a registration date
  // within the reporting period.
  if ($form_content == '4') {
    if (!$row['regdate'] || $row['regdate'] < $from_date ||
      $row['regdate'] > $to_date) return;
  }

  // If first instance of this key, initialize its arrays.
  if (empty($areport[$key])) {
    $areport[$key] = array();
    $areport[$key]['.prp'] = 0;       // previous pid
    $areport[$key]['.wom'] = 0;       // number of services for women
    $areport[$key]['.men'] = 0;       // number of services for men
    $areport[$key]['.age2'] = array(0,0);               // age array
    $areport[$key]['.age9'] = array(0,0,0,0,0,0,0,0,0); // age array
    foreach ($arr_show as $askey => $dummy) {
      if (substr($askey, 0, 1) == '.') continue;
      $areport[$key][$askey] = array();
    }
  }

  // If we are counting unique clients, new acceptors or new clients, then
  // require a unique patient.
  if ($form_content == '2' || $form_content == '3' || $form_content == '4') {
    if ($row['pid'] == $areport[$key]['.prp']) return;
  }

  // Flag this patient as having been encountered for this report row.
  // $areport[$key]['prp'] = $row['pid'];
  $areport[$key]['.prp'] = $row['pid'];

  // Increment the correct sex category.
  if (strcasecmp($row['sex'], 'Male') == 0)
    $areport[$key]['.men'] += $quantity;
  else
    $areport[$key]['.wom'] += $quantity;

  // Increment the correct age categories.
  $age = getAge(fixDate($row['DOB']), $row['encdate']);
  $i = min(intval(($age - 5) / 5), 8);
  if ($age < 10) $i = 0;
  $areport[$key]['.age9'][$i] += $quantity;
  $i = $age < 25 ? 0 : 1;
  $areport[$key]['.age2'][$i] += $quantity;

  foreach ($arr_show as $askey => $dummy) {
    if (substr($askey, 0, 1) == '.') continue;
    $status = empty($row[$askey]) ? 'Unspecified' : $row[$askey];
    $areport[$key][$askey][$status] += $quantity;
    $arr_titles[$askey][$status] += $quantity;
  }
}

// This is called for each IPPF service code that is selected.
//
function process_ippf_code($row, $code, $quantity=1) {
  global $areport, $arr_titles, $form_by, $form_content, $contra_group_name;

  $key = 'Unspecified';

  // SRH including Family Planning
  //
  if ($form_by === '1') {
    if (preg_match('/^1/', $code)) {
      $key = xl('SRH - Family Planning');
    }
    else if (preg_match('/^2/', $code)) {
      $key = xl('SRH Non Family Planning');
    }
    else {
      if ($form_content != 5) return;
    }
  }

  // General Service Category
  //
  else if ($form_by === '3') {
    if (preg_match('/^1/', $code)) {
      $key = xl('SRH - Family Planning');
    }
    else if (preg_match('/^2/', $code)) {
      $key = xl('SRH Non Family Planning');
    }
    else if (preg_match('/^3/', $code)) {
      $key = xl('Non-SRH Medical');
    }
    else if (preg_match('/^4/', $code)) {
      $key = xl('Non-SRH Non-Medical');
    }
    else {
      $key = xl('Invalid Service Codes');
    }
  }

  // Abortion-Related Category
  //
  else if ($form_by === '13') {
    if (preg_match('/^252221/', $code)) {
      $key = xl('Pre-Abortion Counseling');
    }
    else if (preg_match('/^252222/', $code)) {
      $key = xl('Pre-Abortion Consultation');
    }
    else if (preg_match('/^252223/', $code)) {
      $key = xl('Induced Abortion');
    }
    else if (preg_match('/^252224/', $code)) {
      $key = xl('Medical Abortion');
    }
    else if (preg_match('/^252225/', $code)) {
      $key = xl('Incomplete Abortion Treatment');
    }
    else if (preg_match('/^252226/', $code)) {
      $key = xl('Post-Abortion Care');
    }
    else if (preg_match('/^252227/', $code)) {
      $key = xl('Post-Abortion Counseling');
    }
    else if (preg_match('/^25222/', $code)) {
      $key = xl('Other/Generic Abortion-Related');
    }
    else {
      if ($form_content != 5) return;
    }
  }

  // Specific Services. One row for each IPPF code.
  //
  else if ($form_by === '4') {
    $key = $code;
  }

  // Specific Contraceptive Services. One row for each IPPF code.
  //
  else if ($form_by === '104') {
    if ($form_content != 5) {
      // Skip codes not for contraceptive services.
      $tmp = getContraceptiveMethod($code);
      if (empty($tmp)) return;
    }
    $key = $code;
  }

  // Abortion Method.
  //
  else if ($form_by === '5') {
    $key = getAbortionMethod($code);
    if (empty($key)) {
      // If not an abortion service then skip unless counting contraceptive products.
      if ($form_content != 5) return;
      $key = 'Unspecified';
    }
  }

  // Contraceptive Method.
  //
  else if ($form_by === '6') {
    $key = getContraceptiveMethod($code);
    if (empty($key)) {
      // If not a contraceptive service then skip unless counting contraceptive products.
      if ($form_content != 5) return;
      $key = 'Unspecified';
    }
    $key = '{' . $contra_group_name . '}' . $key;
  }

  // Contraceptive method for new contraceptive adoption following abortion.
  // Get it from the IPPF code if there is a suitable recent abortion service
  // or GCAC form.
  //
  else if ($form_by === '7') {
    $key = getContraceptiveMethod($code);
    if (empty($key)) return;
    $key = '{' . $contra_group_name . '}' . $key;
    $patient_id = $row['pid'];
    $encdate = $row['encdate'];
    // Skip this if no recent gcac service nor gcac form with acceptance.
    // Include incomplete abortion treatment services per AM's discussion
    // with Dr. Celal on 2011-04-19.
    if (!hadRecentAbService($patient_id, $encdate, true)) {
      $query = "SELECT COUNT(*) AS count " .
        "FROM forms AS f, form_encounter AS fe, lbf_data AS d " .
        "WHERE f.pid = '$patient_id' AND " .
        "f.formdir = 'LBFgcac' AND " .
        "f.deleted = 0 AND " .
        "fe.pid = f.pid AND fe.encounter = f.encounter AND " .
        "fe.date <= '$encdate' AND " .
        "DATE_ADD(fe.date, INTERVAL 14 DAY) > '$encdate' AND " .
        "d.form_id = f.form_id AND " .
        "d.field_id = 'client_status' AND " .
        "( d.field_value = 'maaa' OR d.field_value = 'refout' )";
      $irow = sqlQuery($query);
      if (empty($irow['count'])) return;
    }
  }

  // Post-Abortion Care and Followup by Source.
  // Requirements just call for counting sessions, but this way the columns
  // can be anything - age category, religion, whatever.
  //
  else if ($form_by === '8') {
    if (preg_match('/^25222[567]/', $code)) { // care, followup and incomplete abortion treatment
      $key = getGcacClientStatus($row);
    } else {
      return;
    }
  }

  // Pre-Abortion Counseling.  Three possible situations:
  //   Provided abortion in the MA clinics
  //   Referred to other service providers (govt,private clinics)
  //   Decided not to have the abortion
  //
  else if ($form_by === '12') {
    if (preg_match('/^252221/', $code)) { // all pre-abortion counseling
      $key = getGcacClientStatus($row);
    } else {
      return;
    }
  }

  else {
    return; // no match, so do nothing
  }

  // OK we now have the reporting key for this issue.
  loadColumnData($key, $row, $quantity);

} // end function process_ippf_code()

// This is called for each MA service code that is selected.
//
function process_ma_code($row) {
  global $form_by, $arr_content, $form_content;

  $key = 'Unspecified';

  // One row for each service category.
  //
  if ($form_by === '101') {
    if (!empty($row['lo_title'])) $key = xl($row['lo_title']);
  }

  // Specific Services. One row for each MA code.
  //
  else if ($form_by === '102') {
    $key = $row['code'];
  }

  // One row for each referral source.
  //
  else if ($form_by === '103') {
    $key = $row['referral_source'];
  }

  // Just one row.
  //
  else if ($form_by === '2') {
    $key = $arr_content[$form_content];
  }

  // Patient Name.
  //
  else if ($form_by === '17') {
    $key = $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'];
  }

  else {
    return;
  }

  loadColumnData($key, $row);
}

function LBFgcac_query($pid, $encounter, $name) {
  $query = "SELECT d.form_id, d.field_value " .
    "FROM forms AS f, form_encounter AS fe, lbf_data AS d " .
    "WHERE f.pid = '$pid' AND " .
    "f.encounter = '$encounter' AND " .
    "f.formdir = 'LBFgcac' AND " .
    "f.deleted = 0 AND " .
    "fe.pid = f.pid AND fe.encounter = f.encounter AND " .
    "d.form_id = f.form_id AND " .
    "d.field_id = '$name'";
  return sqlStatement($query);
}

function LBFgcac_title($form_id, $field_id, $list_id) {
  $query = "SELECT lo.title " .
    "FROM lbf_data AS d, list_options AS lo WHERE " .
    "d.form_id = '$form_id' AND " .
    "d.field_id = '$field_id' AND " .
    "lo.list_id = '$list_id' AND " .
    "lo.option_id = d.field_value " .
    "LIMIT 1";
  $row = sqlQuery($query);
  return empty($row['title']) ? '' : xl_list_label($row['title']);
}

// This is called for each encounter that is selected.
//
function process_visit($row) {
  global $form_by, $form_content;

  // New contraceptive method following abortion.  These should only be
  // present for inbound referrals.
  //
  if ($form_by === '7') {
    // We think this case goes away, but not sure yet.
    /*****************************************************************
    $dres = LBFgcac_query($row['pid'], $row['encounter'], 'contrameth');
    while ($drow = sqlFetchArray($dres)) {
      $a = explode('|', $drow['field_value']);
      foreach ($a as $methid) {
        if (empty($methid)) continue;
        $crow = sqlQuery("SELECT title FROM list_options WHERE " .
          "list_id = 'contrameth' AND option_id = '$methid'");
        $key = $crow['title'];
        if (empty($key)) $key = xl('Indeterminate');
        loadColumnData($key, $row);
      }
    }
    *****************************************************************/
  }

  // Complications of abortion by abortion method and complication type.
  // These may be noted either during recovery or during a followup visit.
  // Note: If there are multiple complications, they will all be reported.
  //
  else if ($form_by === '11') {
    $dres = LBFgcac_query($row['pid'], $row['encounter'], 'complications');
    while ($drow = sqlFetchArray($dres)) {
      $a = explode('|', $drow['field_value']);
      foreach ($a as $complid) {
        if (empty($complid)) continue;
        $crow = sqlQuery("SELECT title FROM list_options WHERE " .
          "list_id = 'complication' AND option_id = '$complid'");
        $abtype = LBFgcac_title($drow['form_id'], 'in_ab_proc', 'in_ab_proc');
        if (empty($abtype)) $abtype = xl('Indeterminate');
        $key = "$abtype / " . xl_list_label($crow['title']);
        loadColumnData($key, $row);
      }
    }
  }

  // Patient Name.
  //
  else if ($form_by === '17') {
    $key = $row['lname'] . ', ' . $row['fname'] . ' ' . $row['mname'];
    // If content is services then quantity = 0 because a visit is not a service.
    loadColumnData($key, $row, $form_content == 1 ? 0 : 1);
  }
}

// This is called for each selected referral.
// Row keys are the first specified MA code, if any.
//
function process_referral($row) {
  global $form_by;
  $key = 'Unspecified';

  // For followups we care about the actual service provided, otherwise
  // the requested service.
  $related_code = $form_by === '20' ?
    $row['reply_related_code'] : $row['refer_related_code'];

  if (!empty($related_code)) {
    $relcodes = explode(';', $related_code);
    foreach ($relcodes as $codestring) {
      if ($codestring === '') continue;
      list($codetype, $code) = explode(':', $codestring);

      if ($codetype == 'REF') {
        // In the case of a REF code, look up the associated IPPF code.
        $rrow = sqlQuery("SELECT related_code FROM codes WHERE " .
          "code_type = '16' AND code = '$code' AND active = 1 " .
          "ORDER BY id LIMIT 1");
        if (!empty($rrow['related_code'])) {
          list($codetype, $code) = explode(':', $rrow['related_code']);
        }
      }

      // Alternatively a direct IPPF code is also supported.
      if ($codetype !== 'IPPF') continue;

      if ($form_by === '1') {
        if (preg_match('/^[12]/', $code)) {
          $key = xl('SRH Referrals');
          loadColumnData($key, $row);
          break;
        }
      }
      else { // $form_by is 9/14 (internal) or 10/15/20 (external) referrals
        $key = $code;
        break;
      }
    } // end foreach
  }

  if ($form_by !== '1') loadColumnData($key, $row);
}

function uses_description($form_by) {
  return ($form_by === '4'  || $form_by === '102' || $form_by === '9' ||
    $form_by === '10' || $form_by === '14' || $form_by === '15' ||
    $form_by === '20' || $form_by === '104');
}

function writeSubtotals($last_group, &$asubtotals, $form_by) {
  if ($last_group) {
    genStartRow("bgcolor='#dddddd'");
    if (uses_description($form_by)) {
      genHeadCell(array(xl('Subtotals for') . " $last_group", ''));
    } else {
      genHeadCell(xl('Subtotals for') . " $last_group", 'left', 2);
    }
    for ($cnum = 0; $cnum < count($asubtotals); ++$cnum) {
      genHeadCell($asubtotals[$cnum], 'right');
    }
    genEndRow();
  }
}

$arr_show   = array(
  '.total' => array('title' => xl('Total')),
  '.age2'  => array('title' => xl('Age Category') . ' (2)'),
  '.age9'  => array('title' => xl('Age Category') . ' (9)'),
); // info about selectable columns

$arr_titles = array(); // will contain column headers

// Query layout_options table to generate the $arr_show table.
// Table key is the field ID.
$lres = sqlStatement("SELECT field_id, title, data_type, list_id, description " .
  "FROM layout_options WHERE " .
  "form_id = 'DEM' AND uor > 0 AND field_id NOT LIKE 'em%' " .
  "ORDER BY group_name, seq, title");
while ($lrow = sqlFetchArray($lres)) {
  $fid = $lrow['field_id'];
  if ($fid == 'fname' || $fid == 'mname' || $fid == 'lname') continue;
  if (!empty($lrow['title'])) $lrow['title'] = xl_layout_label($lrow['title']);
  $arr_show[$fid] = $lrow;
  $arr_titles[$fid] = array();
}

  // If we are doing the CSV export then generate the needed HTTP headers.
  // Otherwise generate HTML.
  //
  if ($form_output == 3) {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=service_statistics_report.csv");
    header("Content-Description: File Transfer");
  }
  else {
?>
<html>
<head>
<?php html_header_show(); ?>
<title><?php echo $report_title; ?></title>
<style type="text/css">@import url(../../library/dynarch_calendar.css);</style>
<style type="text/css">
 body       { font-family:sans-serif; font-size:10pt; font-weight:normal }
 .dehead    { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:bold }
 .detail    { color:#000000; font-family:sans-serif; font-size:10pt; font-weight:normal }
</style>
<script type="text/javascript" src="../../library/textformat.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_en.js"></script>
<script type="text/javascript" src="../../library/dynarch_calendar_setup.js"></script>
<script language="JavaScript">
 var mypcc = '<?php echo $GLOBALS['phone_country_code'] ?>';

 // Begin experimental code

 function selectByValue(sel, val) {
  for (var i = 0; i < sel.options.length; ++i) {
   if (sel.options[i].value == val) sel.options[i].selected = true;
  }
 }

 function selreport() {
  var f = document.forms[0];
  var isdis = 'visible';
  var s = f.form_report;
  var v = (s.selectedIndex < 0) ? '' : s.options[s.selectedIndex].value;
  if (v.length > 0) {
   isdis = 'hidden';
   var a = v.split("|");
   f.form_content.selectedIndex = -1;
   f.form_by.selectedIndex = -1;
   f['form_show[]'].selectedIndex = -1;
   selectByValue(f.form_content, a[0]);
   selectByValue(f['form_by[]'], a[1]);
   for (var i = 2; i < a.length; ++i) {
    selectByValue(f['form_show[]'], a[i]);
   }
  }
  f['form_by[]'].style.visibility = isdis;
  f.form_content.style.visibility = isdis;
  f['form_show[]'].style.visibility = isdis;
 }

 // End experimental code

</script>
</head>

<body leftmargin='0' topmargin='0' marginwidth='0' marginheight='0'>

<center>

<h2><?php echo $report_title; ?></h2>

<form name='theform' method='post'
 action='ippf_statistics.php?t=<?php echo $report_type ?>'>

<table border='0' cellspacing='5' cellpadding='1'>

 <!-- Begin experimental code -->
 <tr<?php if (empty($arr_report)) echo " style='display:none'"; ?>>
  <td valign='top' class='dehead' nowrap>
   <?php xl('Report','e'); ?>:
  </td>
  <td valign='top' class='detail' colspan='3'>
   <select name='form_report' title='Predefined reports' onchange='selreport()'>
<?php
  echo "    <option value=''>" . xl('Custom') . "</option>\n";
  foreach ($arr_report as $key => $value) {
    echo "    <option value='$key'";
    if ($key == $form_report) echo " selected";
    echo ">" . $value . "</option>\n";
  }
?>
   </select>
  </td>
  <td valign='top' class='detail'>
   &nbsp;
  </td>
 </tr>
 <!-- End experimental code -->

 <tr>
  <td valign='top' class='dehead' nowrap>
   <?php xl('Rows','e'); ?>:
  </td>
  <td valign='top' class='detail'>
   <select name='form_by[]' size='3' multiple
    title='<?php xl('Hold down Ctrl to select multiple reports','e'); ?>'>
<?php
  foreach ($arr_by as $key => $value) {
    echo "    <option value='$key'";
    if (is_array($form_by_arr) && in_array($key, $form_by_arr)) echo " selected";
    echo ">" . $value . "</option>\n";
  }
?>
   </select>
  </td>
  <td valign='top' class='dehead' nowrap>
   <?php xl('Content','e'); ?>:
  </td>
  <td valign='top' class='detail'>
   <select name='form_content' title='<?php xl('What is to be counted?','e'); ?>'>
<?php
  foreach ($arr_content as $key => $value) {
    echo "    <option value='$key'";
    if ($key == $form_content) echo " selected";
    echo ">$value</option>\n";
  }
?>
   </select>
  </td>
  <td valign='top' class='detail'>
   &nbsp;
  </td>
 </tr>

 <tr>
  <td valign='top' class='dehead' nowrap>
   <?php xl('Columns','e'); ?>:
  </td>
  <td valign='top' class='detail'>
   <select name='form_show[]' size='4' multiple
    title='<?php xl('Hold down Ctrl to select multiple items','e'); ?>'>
<?php
  foreach ($arr_show as $key => $value) {
    $title = $value['title'];
    if (empty($title) || $key == 'title') $title = $value['description'];
    echo "    <option value='$key'";
    if (is_array($form_show) && in_array($key, $form_show)) echo " selected";
    echo ">$title</option>\n";
  }
?>
   </select>
  </td>
  <td valign='top' class='dehead' nowrap>
   <?php xl('Filters','e'); ?>:
  </td>
  <td colspan='2' class='detail' style='border-style:solid;border-width:1px;border-color:#cccccc'>
   <table>
    <tr>
     <td valign='top' class='detail' nowrap>
      <?php xl('Sex','e'); ?>:
     </td>
     <td class='detail' valign='top'>
      <select name='form_sexes' title='<?php xl('To filter by sex','e'); ?>'>
<?php
  foreach (array(3 => xl('Men and Women'), 1 => xl('Women Only'), 2 => xl('Men Only')) as $key => $value) {
    echo "       <option value='$key'";
    if ($key == $form_sexes) echo " selected";
    echo ">$value</option>\n";
  }
?>
      </select>
     </td>
    </tr>
    <tr>
     <td valign='top' class='detail' nowrap>
      <?php xl('Facility','e'); ?>:
     </td>
     <td valign='top' class='detail'>
<?php
 // Build a drop-down list of facilities.
 //
 $query = "SELECT id, name FROM facility ORDER BY name";
 $fres = sqlStatement($query);
 echo "      <select name='form_facility'>\n";
 echo "       <option value=''>-- " . xl('All Facilities') . " --\n";
 while ($frow = sqlFetchArray($fres)) {
  $facid = $frow['id'];
  echo "       <option value='$facid'";
  if ($facid == $_POST['form_facility']) echo " selected";
  echo ">" . $frow['name'] . "\n";
 }
 echo "      </select>\n";
?>
     </td>
    </tr>
    <tr>
     <td colspan='2' class='detail' nowrap>
      <?php xl('From','e'); ?>
      <input type='text' name='form_from_date' id='form_from_date' size='10' value='<?php echo $from_date ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='Start date yyyy-mm-dd'>
      <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
       id='img_from_date' border='0' alt='[?]' style='cursor:pointer'
       title='<?php xl('Click here to choose a date','e'); ?>'>
      <?php xl('To','e'); ?>
      <input type='text' name='form_to_date' id='form_to_date' size='10' value='<?php echo $to_date ?>'
       onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='End date yyyy-mm-dd'>
      <img src='../pic/show_calendar.gif' align='absbottom' width='24' height='22'
       id='img_to_date' border='0' alt='[?]' style='cursor:pointer'
       title='<?php xl('Click here to choose a date','e'); ?>'>
     </td>
    </tr>
   </table>
  </td>
 </tr>

 <tr>
  <td valign='top' class='dehead' nowrap>
   <?php xl('To','e'); ?>:
  </td>
  <td colspan='3' valign='top' class='detail' nowrap>
<?php
foreach (array(1 => xl('Screen'), 2 => xl('Printer'), 3 => xl('Export File')) as $key => $value) {
  echo "   <input type='radio' name='form_output' value='$key'";
  if ($key == $form_output) echo ' checked';
  echo " />$value &nbsp;";
}
?>
  </td>
  <td align='right' valign='top' class='detail' nowrap>
   <input type='submit' name='form_submit' value='<?php xl('Submit','e'); ?>'
    title='<?php xl('Click to generate the report','e'); ?>' />
  </td>
 </tr>
 <tr>
  <td colspan='5' height="1">
  </td>
 </tr>
</table>
<?php
  } // end not export

// If refresh or export...
//
if ($_POST['form_submit']) {

  // Start table.
  if ($form_output != 3) {
    echo "<table border='0' cellpadding='1' cellspacing='2' width='98%'>\n";
  } // end not csv export

  $report_col_count = 0;

  // Start report loop.  This loops once for each report selected from the
  // "Rows" list.
  //
  foreach ($form_by_arr as $form_by) {

    // This will become the array of reportable values.
    $areport = array();

    // This accumulates the bottom line totals.
    $atotals = array();

    $pd_fields = '';
    foreach ($arr_show as $askey => $asval) {
      if (substr($askey, 0, 1) == '.') continue;
      if ($askey == 'regdate' || $askey == 'sex' || $askey == 'DOB' ||
        $askey == 'lname' || $askey == 'fname' || $askey == 'mname' ||
        $askey == 'contrastart' || $askey == 'ippfconmeth' ||
        $askey == 'referral_source') continue;
      $pd_fields .= ', pd.' . $askey;
    }

    $sexcond = '';
    if ($form_sexes == '1') $sexcond = "AND pd.sex NOT LIKE 'Male' ";
    else if ($form_sexes == '2') $sexcond = "AND pd.sex LIKE 'Male' ";

    // In the case where content is contraceptive product sales, we
    // scan product sales at the top level because it is important to
    // account for each of them only once.  For each sale we determine
    // the one and only IPPF code representing the primary related
    // contraceptive service, and that might be either a service in
    // the Tally Sheet or the IPPF code attached to the product.
    //
    if ($form_content == 5) { // sales of contraceptive products
      $query = "SELECT " .
        "ds.pid, ds.encounter, ds.sale_date, ds.quantity, " .
        "d.cyp_factor, d.related_code, " . 
        "pd.regdate, pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
        "pd.referral_source$pd_fields, " .
        "fe.date AS encdate, fe.provider_id " .
        "FROM drug_sales AS ds " .
        "JOIN drugs AS d ON d.drug_id = ds.drug_id " .
        "JOIN patient_data AS pd ON pd.pid = ds.pid $sexcond" .
        "LEFT JOIN form_encounter AS fe ON fe.pid = ds.pid AND fe.encounter = ds.encounter " .
        "WHERE ds.sale_date >= '$from_date' AND " .
        "ds.sale_date <= '$to_date' AND " .
        "ds.pid > 0 AND ds.quantity != 0";

      if ($form_facility) {
        $query .= " AND fe.facility_id = '$form_facility'";
      }
      $query .= " ORDER BY ds.pid, ds.encounter, ds.drug_id";
      $res = sqlStatement($query);

      while ($row = sqlFetchArray($res)) {
        $desired = false;
        $prodcode = '';
        if ($row['cyp_factor'] > 0) {
          $desired = true;
        }
        $tmp = getRelatedContraceptiveCode($row);
        if (!empty($tmp)) {
          $desired = true;
          $prodcode = $tmp;
        }
        if (!$desired) continue; // skip if not a contraceptive product

        // If there is a visit and it has a contraceptive service use that, else $prodcode.
        if (!empty($row['encounter'])) {
          $query = "SELECT " .
            "b.code_type, b.code, c.related_code " .
            "FROM billing AS b " .
            "LEFT OUTER JOIN codes AS c ON c.code_type = '12' AND " .
            "c.code = b.code AND c.modifier = b.modifier " .
            "WHERE b.pid = " . (0 + $row['pid']) . " AND " .
            "b.encounter = " . (0 + $row['encounter']) . " AND " .
            "b.activity = 1 AND b.code_type = 'MA' " .
            "ORDER BY b.code";
          $bres = sqlStatement($query);
          while ($brow = sqlFetchArray($bres)) {
            $tmp = getRelatedContraceptiveCode($brow);
            if (!empty($tmp)) {
              $prodcode = $tmp;
              break;
            }
          }
        }

        // At this point $prodcode is the desired IPPF code, or empty if none.
        process_ippf_code($row, $prodcode, $row['quantity']);

      }
    }

    // Get referrals and related patient data.
    if ($form_content != 5 && ($form_by === '9' || $form_by === '10' ||
      $form_by === '14' || $form_by === '15' || $form_by === '20' ||
      $form_by === '1'))
    {
      $exttest = "t.refer_external = '2'"; // outbound external
      $datefld = "t.refer_date";

      if ($form_by === '9') {
        $exttest = "t.refer_external = '3'"; // outbound internal
      }
      else if ($form_by === '14') {
        $exttest = "t.refer_external = '5'"; // inbound internal
      }
      else if ($form_by === '15') {
        $exttest = "t.refer_external = '4'"; // inbound external
      }
      else if ($form_by === '20') {
        $datefld = "t.reply_date";
      }

      $query = "SELECT " .
        "t.pid, t.refer_related_code, t.reply_related_code, " .
        "pd.regdate, pd.referral_source, " .
        "pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname $pd_fields " .
        "FROM transactions AS t " .
        "JOIN patient_data AS pd ON pd.pid = t.pid $sexcond" .
        "WHERE t.title = 'Referral' AND $datefld IS NOT NULL AND " .
        "$datefld >= '$from_date' AND $datefld <= '$to_date' AND $exttest " .
        "ORDER BY t.pid, t.id";
      $res = sqlStatement($query);
      while ($row = sqlFetchArray($res)) {
        process_referral($row);
      }
    }

    // Reporting New Acceptors by Contraceptive Method (or method after abortion)
    // is a special case that gets one method on each contraceptive start date.
    //
    if ($form_content == 3) {
     if ($form_by === '6' || $form_by === '7') {
      /***************************************************************
      // This gets us all MA codes, with encounter and patient
      // info attached and grouped by patient and encounter.
      $query = "SELECT " .
        "pd.pid, pd.regdate, " .
        "pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
        "pd.contrastart, pd.ippfconmeth, pd.referral_source$pd_fields " .
        "FROM patient_data AS pd " .
        "WHERE pd.contrastart IS NOT NULL AND " .
        "pd.contrastart != '0000-00-00' AND " .
        "pd.contrastart >= '$from_date' AND " .
        "pd.contrastart <= '$to_date' $sexcond";
      $query .= "ORDER BY pd.pid";
      $res = sqlStatement($query);
      //
      while ($row = sqlFetchArray($res)) {
        $contrastart = $row['contrastart'];
        $ippfconmeth = $row['ippfconmeth'];
        $thispid     = $row['pid'];
        $row['encdate'] = "$contrastart 00:00:00";
        //
        if ($ippfconmeth) {
          process_ippf_code($row, $ippfconmeth);
        }
        // If no method saved in patient_data, get it from the visit.
        else {
          $contraception_code = '';
          $contraception_cyp  = -1;
          $query = "SELECT " .
            "pd.regdate, " .
            "pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
            "pd.contrastart, pd.ippfconmeth, pd.referral_source$pd_fields, " .
            "fe.pid, fe.encounter, fe.date AS encdate, " .
            "f.user AS provider, " .
            "b.code_type, b.code, c.related_code, lo.title AS lo_title " .
            "FROM form_encounter AS fe " .
            "JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND " .
            "f.formdir = 'newpatient' AND f.form_id = fe.id AND f.deleted = 0 " .
            "JOIN patient_data AS pd ON pd.pid = fe.pid $sexcond" .
            "LEFT OUTER JOIN billing AS b ON " .
            "b.pid = fe.pid AND b.encounter = fe.encounter AND b.activity = 1 " .
            "AND b.code_type = 'MA' " .
            "LEFT OUTER JOIN codes AS c ON b.code_type = 'MA' AND c.code_type = '12' AND " .
            "c.code = b.code AND c.modifier = b.modifier " .
            "LEFT OUTER JOIN list_options AS lo ON " .
            "lo.list_id = 'superbill' AND lo.option_id = c.superbill " .
            "WHERE fe.pid = '$thispid' AND fe.date >= '$contrastart 00:00:00' AND " .
            "fe.date <= '$contrastart 23:59:59' ";
          if ($form_facility) {
            $query .= "AND fe.facility_id = '$form_facility' ";
          }
          $query .= "ORDER BY fe.encounter, b.code";
          // echo "<!-- $query -->\n"; // debugging
          $bres = sqlStatement($query);
          while ($brow = sqlFetchArray($bres)) {
            if ($brow['code_type'] === 'MA') {
              if (!empty($brow['related_code'])) {
                $relcodes = explode(';', $brow['related_code']);
                foreach ($relcodes as $codestring) {
                  if ($codestring === '') continue;
                  list($codetype, $relcode) = explode(':', $codestring);
                  if ($codetype !== 'IPPF') continue;
                  // Code below borrowed from function contraceptionClass() in the Fee Sheet.
                  if (
                    preg_match('/^11....110/'    , $relcode) ||
                    preg_match('/^11...[1-5]999/', $relcode) ||
                    preg_match('/^112152010/'    , $relcode) ||
                    preg_match('/^11317[1-2]111/', $relcode) ||
                    preg_match('/^12118[1-2].13/', $relcode) ||
                    preg_match('/^121181999/'    , $relcode) ||
                    preg_match('/^122182.13/'    , $relcode) ||
                    preg_match('/^122182999/'    , $relcode)
                  ) {
                    $tmprow = sqlQuery("SELECT cyp_factor FROM codes WHERE " .
                      "code_type = '11' AND code = '$relcode' LIMIT 1");
                    $cyp = 0 + $tmprow['cyp_factor'];
                    if ($cyp > $contraception_cyp) {
                      $contraception_cyp = $cyp;
                      $contraception_code = $relcode;
                    }
                  }
                  // End borrowed code.
                }
              }
            }
          } // end while
          if ($contraception_code) {
            process_ippf_code($row, $contraception_code);
            // echo "<!-- process_ippf_code(row, $contraception_code) -->\n"; // debugging
          }
        }
      }
      ***************************************************************/

      /***************************************************************
      // This counts instances of "contraception starting" for the MA.  Note that a
      // client could be counted twice, once for nonsurgical and once for surgical.
      // Note also that we filter based on contrastart date, which is usually
      // the same as encounter date but might not be.
      $query = "SELECT " .
        "d2.field_value AS contrastart, d3.field_value AS ippfconmeth, " .
        "fe.pid, fe.encounter, fe.date AS encdate, " .
        "f.user AS provider, " .
        "pd.regdate, pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
        "pd.referral_source$pd_fields " .
        "FROM forms AS f " .
        "JOIN lbf_data AS d1 ON d1.form_id = f.form_id AND d1.field_id = 'contratype' AND " .
        "(d1.field_value = '2' OR d1.field_value = 3) " .
        "JOIN lbf_data AS d2 ON d2.form_id = f.form_id AND d2.field_id = 'contrastart' AND " .
        "d2.field_value IS NOT NULL AND d2.field_value >= '$from_date' AND d2.field_value <= '$to_date' " .
        "LEFT JOIN lbf_data AS d3 ON d3.form_id = f.form_id AND d3.field_id = 'ippfconmeth' " .
        "JOIN form_encounter AS fe ON fe.pid = f.pid AND fe.encounter = f.encounter ";
      if ($form_facility) {
        $query .= "AND fe.facility_id = '$form_facility' ";
      }
      $query .=
        "JOIN patient_data AS pd ON pd.pid = f.pid $sexcond " .
        "WHERE f.formdir = 'LBFcontra' AND f.deleted = 0 " .
        "ORDER BY fe.pid, fe.encounter, f.form_id";
      ***************************************************************/

      // This counts instances of "contraception starting" for the MA.  Note that a
      // client could be counted twice, once for nonsurgical and once for surgical.
      // Note also that we filter based on start date which is the same as encounter
      // date.
      $query = "SELECT " .
        "d1.field_value AS ippfconmeth, " .
        "fe.pid, fe.encounter, fe.date AS encdate, fe.date AS contrastart, " .
        "f.user AS provider, " .
        "pd.regdate, pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
        "pd.referral_source$pd_fields " .
        "FROM forms AS f " .
        "JOIN form_encounter AS fe ON fe.pid = f.pid AND fe.encounter = f.encounter AND " .
        "fe.date IS NOT NULL AND fe.date >= '$from_date' AND fe.date <= '$to_date' ";
      if ($form_facility) {
        $query .= "AND fe.facility_id = '$form_facility' ";
      }
      $query .=
        "JOIN lbf_data AS d1 ON d1.form_id = f.form_id AND d1.field_id = 'newmethod' " .
        "LEFT JOIN lbf_data AS d2 ON d2.form_id = f.form_id AND d2.field_id = 'newmauser' " .
        "JOIN patient_data AS pd ON pd.pid = f.pid $sexcond " .
        "WHERE f.formdir = 'LBFccicon' AND f.deleted = 0 AND " .
        "d1.field_value LIKE '12%' OR (d2.field_value IS NOT NULL AND d2.field_value = '1') " .
        "ORDER BY fe.pid, fe.encounter, f.form_id";

      echo "<!-- $query -->\n"; // debugging
      $res = sqlStatement($query);
      //
      while ($row = sqlFetchArray($res)) {
        $contrastart = $row['contrastart'];
        $ippfconmeth = $row['ippfconmeth'];
        $thispid     = $row['pid'];
        $thisenc     = $row['encounter'];
        // echo "<!-- '$thispid' '$thisenc' '$contrastart' '$ippfconmeth' -->\n"; // debugging

        /*************************************************************
        if ($ippfconmeth) {
          process_ippf_code($row, $ippfconmeth);
        }
        // If no method saved in patient_data, get it from the visit.
        else {
          $contraception_code = '';
          $contraception_cyp  = -1;
          $query = "SELECT " .
            "b.code_type, b.code, c.related_code, lo.title AS lo_title " .
            "FROM billing AS b WHERE " .
            "b.pid = '$thispid' AND b.encounter = '$thisenc' AND b.activity = 1 " .
            "AND b.code_type = 'MA' " .
            "LEFT OUTER JOIN codes AS c ON b.code_type = 'MA' AND c.code_type = '12' AND " .
            "c.code = b.code AND c.modifier = b.modifier " .
            "LEFT OUTER JOIN list_options AS lo ON " .
            "lo.list_id = 'superbill' AND lo.option_id = c.superbill " .
            "ORDER BY b.code";
          // echo "<!-- $query -->\n"; // debugging
          $bres = sqlStatement($query);
          while ($brow = sqlFetchArray($bres)) {
            if ($brow['code_type'] === 'MA') {
              if (!empty($brow['related_code'])) {
                $relcodes = explode(';', $brow['related_code']);
                foreach ($relcodes as $codestring) {
                  if ($codestring === '') continue;
                  list($codetype, $relcode) = explode(':', $codestring);
                  if ($codetype !== 'IPPF') continue;
                  // Code below borrowed from function contraceptionClass() in the Fee Sheet.
                  if (
                    preg_match('/^11....110/'    , $relcode) ||
                    preg_match('/^11...[1-5]999/', $relcode) ||
                    preg_match('/^112152010/'    , $relcode) ||
                    preg_match('/^11317[1-2]111/', $relcode) ||
                    preg_match('/^12118[1-2].13/', $relcode) ||
                    preg_match('/^121181999/'    , $relcode) ||
                    preg_match('/^122182.13/'    , $relcode) ||
                    preg_match('/^122182999/'    , $relcode)
                  ) {
                    $tmprow = sqlQuery("SELECT cyp_factor FROM codes WHERE " .
                      "code_type = '11' AND code = '$relcode' LIMIT 1");
                    $cyp = 0 + $tmprow['cyp_factor'];
                    if ($cyp > $contraception_cyp) {
                      $contraception_cyp = $cyp;
                      $contraception_code = $relcode;
                    }
                  }
                  // End borrowed code.
                }
              }
            }
          } // end while
          if ($contraception_code) {
            process_ippf_code($row, $contraception_code);
            // echo "<!-- process_ippf_code(row, $contraception_code) -->\n"; // debugging
          }
        }
        *************************************************************/

        process_ippf_code($row, $ippfconmeth);

      } // end while
     }
     else { // content is new acceptors but incompatible report type
      $alertmsg = xl("New Acceptors content type is valid only for contraceptive method reporting.");
     }
    } // end if

    else

    if ($form_content != 5 && $form_by !== '9' && $form_by !== '10' &&
      $form_by !== '14' && $form_by !== '15' && $form_by !== '20')
    {
      // This gets us all MA codes, with encounter and patient
      // info attached and grouped by patient and encounter.
      $query = "SELECT " .
        "fe.pid, fe.encounter, fe.date AS encdate, pd.regdate, " .
        "f.user AS provider, " .
        "pd.sex, pd.DOB, pd.lname, pd.fname, pd.mname, " .
        "pd.referral_source$pd_fields, " .
        "b.code_type, b.code, c.related_code, lo.title AS lo_title " .
        "FROM form_encounter AS fe " .
        "JOIN forms AS f ON f.pid = fe.pid AND f.encounter = fe.encounter AND " .
        "f.formdir = 'newpatient' AND f.form_id = fe.id AND f.deleted = 0 " .
        "JOIN patient_data AS pd ON pd.pid = fe.pid $sexcond" .
        "LEFT OUTER JOIN billing AS b ON " .
        "b.pid = fe.pid AND b.encounter = fe.encounter AND b.activity = 1 " .
        "AND b.code_type = 'MA' " .
        "LEFT OUTER JOIN codes AS c ON b.code_type = 'MA' AND c.code_type = '12' AND " .
        "c.code = b.code AND c.modifier = b.modifier " .
        "LEFT OUTER JOIN list_options AS lo ON " .
        "lo.list_id = 'superbill' AND lo.option_id = c.superbill " .
        "WHERE fe.date >= '$from_date 00:00:00' AND " .
        "fe.date <= '$to_date 23:59:59' ";

      if ($form_facility) {
        $query .= "AND fe.facility_id = '$form_facility' ";
      }
      $query .= "ORDER BY fe.pid, fe.encounter, b.code";
      $res = sqlStatement($query);

      $prev_encounter = 0;

      while ($row = sqlFetchArray($res)) {
        if ($row['encounter'] != $prev_encounter) {
          $prev_encounter = $row['encounter'];
          process_visit($row);
        }
        if ($row['code_type'] === 'MA') {
          process_ma_code($row);
          if (!empty($row['related_code'])) {
            $relcodes = explode(';', $row['related_code']);
            foreach ($relcodes as $codestring) {
              if ($codestring === '') continue;
              list($codetype, $code) = explode(':', $codestring);
              if ($codetype !== 'IPPF') continue;
              process_ippf_code($row, $code);
            }
          }
        }
      } // end while
    } // end if

    // Sort everything by key for reporting.
    ksort($areport);
    foreach ($arr_titles as $atkey => $dummy) ksort($arr_titles[$atkey]);

    // Generate a blank row to separate from the previous report.
    if ($report_col_count && $form_output != 3) {
      genStartRow("bgcolor='#ffffff'");
      genHeadCell('&nbsp;', 'left', $report_col_count);
      genEndRow();
    }

    // Generate first column headings line, with category titles.
    //
    genStartRow("bgcolor='#dddddd'");
    /*****
    // If the key is an MA or IPPF code, then add a column for its description.
    if (uses_description($form_by)) {
      genHeadCell(array('', ''));
    } else {
      genHeadCell('', 'left', 2);
    }
    *****/
    genHeadCell($arr_by[$form_by], 'left', 2);
    $report_col_count = 2;
    // Generate headings for values to be shown.
    foreach ($form_show as $value) {
      if ($value == '.total') { // Total Services
        genHeadCell('');
        ++$report_col_count;
      }
      else if ($value == '.age2') { // Age
        genHeadCell($arr_show[$value]['title'], 'center', 2);
        $report_col_count += 2;
      }
      else if ($value == '.age9') { // Age
        genHeadCell($arr_show[$value]['title'], 'center', 9);
        $report_col_count += 9;
      }
      else if ($arr_show[$value]['list_id']) {
        genHeadCell($arr_show[$value]['title'], 'center', count($arr_titles[$value]));
        $report_col_count += count($arr_titles[$value]);
      }
      else if (!empty($arr_titles[$value])) {
        genHeadCell($arr_show[$value]['title'], 'center', count($arr_titles[$value]));
        $report_col_count += count($arr_titles[$value]);
      }
    }
    // if ($form_output != 3) {
      genHeadCell('');
      ++$report_col_count;
    // }
    genEndRow();

    // Generate second column headings line, with individual titles.
    //
    genStartRow("bgcolor='#dddddd'");
    // If the key is an MA or IPPF code, then add a column for its description.
    if (uses_description($form_by)) {
      genHeadCell(array(xl('Code'), xl('Description')));
    } else {
      genHeadCell('', 'left', 2);
    }
    // Generate headings for values to be shown.
    foreach ($form_show as $value) {
      if ($value == '.total') { // Total Services
        genHeadCell(xl('Total'));
      }
      else if ($value == '.age2') { // Age
        genHeadCell(xl('0-24' ), 'right');
        genHeadCell(xl('25+'  ), 'right');
      }
      else if ($value == '.age9') { // Age
        genHeadCell(xl('0-9'  ), 'right');
        genHeadCell(xl('10-14'), 'right');
        genHeadCell(xl('15-19'), 'right');
        genHeadCell(xl('20-24'), 'right');
        genHeadCell(xl('25-29'), 'right');
        genHeadCell(xl('30-34'), 'right');
        genHeadCell(xl('35-39'), 'right');
        genHeadCell(xl('40-44'), 'right');
        genHeadCell(xl('45+'  ), 'right');
      }
      else if ($arr_show[$value]['list_id']) {
        foreach ($arr_titles[$value] as $key => $dummy) {
          genHeadCell(getListTitle($arr_show[$value]['list_id'],$key), 'right');
        }
      }
      else if (!empty($arr_titles[$value])) {
        foreach ($arr_titles[$value] as $key => $dummy) {
          genHeadCell($key, 'right');
        }
      }
    }
    // if ($form_output != 3) {
      genHeadCell(xl('Total'), 'right');
    // }
    genEndRow();

    $encount = 0;

    // These support group subtotals.
    $last_group = '';
    $asubtotals = array();

    foreach ($areport as $key => $varr) {
      $display_key = $key;
      if ($form_output != 3 && $form_content != '2' && $form_content != '3' && $form_content != '4') {
        // TBD: Get group name, if any, for this key.
        // If it is a group change and there is a non-empty $last_group,
        // generate a subtotals line and clear subtotals array.
        // Set $last_group to the current group name.
        $this_group = '';
        if (preg_match('/^{(.*)}(.*)/', $key, $tmp)) {
          $this_group = $tmp[1];
          $display_key = $tmp[2];
        }
        if ($this_group != $last_group) {
          writeSubtotals($last_group, $asubtotals, $form_by);  
          $last_group = $this_group;
          $asubtotals = array();
        }
      }

      $bgcolor = (++$encount & 1) ? "#ddddff" : "#ffdddd";

      $dispkey = $display_key;
      $dispspan = 2;

      // If the key is an MA or IPPF code, then add a column for its description.
      if (uses_description($form_by)) {
        $dispkey = array($display_key, '');
        $dispspan = 1;
        $type = $form_by === '102' ? 12 : 11; // MA or IPPF
        $crow = sqlQuery("SELECT code_text FROM codes WHERE " .
          "code_type = '$type' AND code = '$display_key' ORDER BY id LIMIT 1");
        if (!empty($crow['code_text'])) $dispkey[1] = $crow['code_text'];
      }

      genStartRow("bgcolor='$bgcolor'");

      genAnyCell($dispkey, 'left', 'detail', $dispspan);

      // This is the column index for accumulating column totals.
      $cnum = 0;
      $totalsvcs = $areport[$key]['.wom'] + $areport[$key]['.men'];

      // Generate data for this row.
      foreach ($form_show as $value) {
        // if ($value == '1') { // Total Services
        if ($value == '.total') { // Total Services
          genNumCell($totalsvcs, $cnum++);
        }
        else if ($value == '.age2') { // Age
          for ($i = 0; $i < 2; ++$i) {
            genNumCell($areport[$key]['.age2'][$i], $cnum++);
          }
        }
        else if ($value == '.age9') { // Age
          for ($i = 0; $i < 9; ++$i) {
            genNumCell($areport[$key]['.age9'][$i], $cnum++);
          }
        }
        else if (!empty($arr_titles[$value])) {
          foreach ($arr_titles[$value] as $title => $dummy) {
            genNumCell($areport[$key][$value][$title], $cnum++);
          }
        }
      }

      // Write the Total column data.
      // if ($form_output != 3) {
        $atotals[$cnum]    += $totalsvcs;
        $asubtotals[$cnum] += $totalsvcs;
        genAnyCell($totalsvcs, 'right', 'dehead');
      // }

      genEndRow();
    } // end foreach

    // If we are exporting or counting unique clients, new acceptors or new clients,
    // then the totals line is skipped.
    //
    if ($form_output != 3 && $form_content != '2' && $form_content != '3' && $form_content != '4') {

      // If there is a non-empty $last_group, generate a subtotals line.
      writeSubtotals($last_group, $asubtotals, $form_by);  

      // Generate the line of totals.
      genStartRow("bgcolor='#dddddd'");

      // If the key is an MA or IPPF code, then add a column for its description.
      $tmp = xl('Total') . ' ' . $arr_content[$form_content];
      if (uses_description($form_by)) {
        genHeadCell(array($tmp, ''));
      } else {
        genHeadCell($tmp, 'left', 2);
      }

      for ($cnum = 0; $cnum < count($atotals); ++$cnum) {
        genHeadCell($atotals[$cnum], 'right');
      }
      genEndRow();
    }

  } // end foreach $form_by_arr

  // End of table.
  if ($form_output != 3) {
    echo "</table>\n";
  }

} // end of if refresh or export

  if ($form_output != 3) {
?>
</form>
</center>

<script language='JavaScript'>
 selreport();
 Calendar.setup({inputField:"form_from_date", ifFormat:"%Y-%m-%d", button:"img_from_date"});
 Calendar.setup({inputField:"form_to_date", ifFormat:"%Y-%m-%d", button:"img_to_date"});

<?php
  if ($alertmsg) {
    echo "alert('" . htmlentities($alertmsg) . "');\n";
  }
?>

<?php if ($form_output == 2) { ?>
 window.print();
<?php } ?>
</script>

</body>
</html>
<?php
  } // end not export
?>

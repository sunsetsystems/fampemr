<?php
// Copyright (C) 2012 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This provides enhancement functions for the LBFccicon visit form.
// It is invoked by interface/forms/LBF/new.php.

require_once("../../../custom/code_types.inc.php");

// These variables are used to compute the service with highest CYP.
//
$contraception_code = '';
$contraception_cyp  = -1;
$contraception_prov = 0;

// This is called for each service in the visit to determine the IPPF code
// of the service with highest CYP.
//
function _LBFccicon_contraception_scan($code_type, $code, $provider) {
  global $code_types;
  global $contraception_code, $contraception_cyp, $contraception_prov;

  $sql = "SELECT related_code FROM codes WHERE " .
    "code_type = '" . $code_types[$code_type]['id'] .
    "' AND code = '$code' LIMIT 1";
  $codesrow = sqlQuery($sql);

  if (!empty($codesrow['related_code']) && $code_type == 'MA') {
    $relcodes = explode(';', $codesrow['related_code']);
    foreach ($relcodes as $relstring) {
      if ($relstring === '') continue;
      list($reltype, $relcode) = explode(':', $relstring);
      if ($reltype !== 'IPPF') continue;
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
          $contraception_cyp  = $cyp;
          $contraception_code = $relcode;
          $contraception_prov = $provider;
        }
      }
    }
  }
}

// The purpose of this function is to create JavaScript for the <head>
// section of the page.  This defines desired javaScript functions.
//
function LBFccicon_javascript() {
  global $formid;

  // Create an associative array of contrameth mapping values.  These are regex
  // patterns used to match ippfconmeth list items with contrameth list items.
  // Also use a pattern of 00000 to indicate not a modern method.
  echo "var contraMapping = new Object();\n";
  $res = sqlStatement("SELECT option_id, option_value, mapping FROM list_options WHERE " .
    "list_id = 'contrameth' ORDER BY seq, title");
  while ($row = sqlFetchArray($res)) {
    $mapping = '00000';
    if ($row['option_value']) { // if modern method
      $i = strpos($row['mapping'], ':');
      if ($i === FALSE) continue;
      $mapping = substr($row['mapping'], $i + 1);
    }
    echo "contraMapping['" . $row['option_id'] . "'] = '$mapping';\n";
  }

  echo "
var pastmodern_autoset = false;

// Respond to selection of a current method.
// If it is unassigned or not modern, ask if a modern method was used anywhere before.
// Otherwise if new method != current then ask reason for method change.
function current_method_changed() {
 var f = document.forms[0];
 f.form_pastmodern.disabled = true;
 f.form_mcreason.disabled = true;
 if (f.form_curmethod.selectedIndex <= 0 || contraMapping[f.form_curmethod.value] == '00000') {
  if (!pastmodern_autoset) {
   // Enable past use question iff no modern current method and only if the
   // global option is set to record all acceptors new to modern contraception.
   f.form_pastmodern.disabled = " . ($GLOBALS['gbl_new_acceptor_policy'] == '3' ? 'false' : 'true') . ";
  }
 }
 else {
  // Here there is a current modern method.  Use its regex pattern to decide if
  // the new method is different.  If so, enable the reason for change selector.
  // The pattern should not be missing, but we also enable in that case.
  var pattern = contraMapping[f.form_curmethod.value];
  if (!(pattern && f.form_newmethod.value.match('^' + pattern))) {
   f.form_mcreason.disabled = false;
  }
 }
}
";

  echo "
// Enable some form fields before submitting.
// This is because disabled fields do not submit their data, however
// we do want to save the default values that were set for them.
function mysubmit() {
 var f = document.forms[0];
 f.form_newmauser.disabled = false;
 f.form_pastmodern.disabled = false;
 f.form_newmethod.disabled = false;
 f.form_provider.disabled = false;
 f.form_mcreason.disabled = false;
 top.restoreSession();
 return true;
}
";
}

// The purpose of this function is to create JavaScript that is run
// once when the page is loaded.
//
function LBFccicon_javascript_onload() {
  global $formid, $pid, $encounter;
  global $contraception_code, $contraception_cyp, $contraception_prov;

  $encrow = sqlQuery("SELECT date, provider_id FROM form_encounter " .
    "WHERE pid = '$pid' AND encounter = '$encounter' " .
    "ORDER BY id DESC LIMIT 1");
  $encdate = $encrow['date'];

  $billresult = getBillingByEncounter($pid, $encounter, "*");
  foreach ($billresult as $iter) {
    _LBFccicon_contraception_scan($iter["code_type"], trim($iter["code"]), $iter['provider_id']);
  }

  // If no provider at the line level, use the encounter's default provider.
  if (empty($contraception_prov)) {
    $contraception_prov = 0 + $encrow['provider_id'];
  }

  // Normalize the IPPF service code to what we use in our list of methods.

  $newdisabled = 'false';
  if (!empty($contraception_code)) {
    // A contraception service exists, so will not ask for it or the provider here.
    $newdisabled = 'true';
    if (preg_match('/^12/', $contraception_code)) { // surgical methods
      // Identify the method with the IPPF code for the corresponding surgical procedure.
      $contraception_code = substr($contraception_code, 0, 7) . '13';
    }
    else { // nonsurgical methods
      // Identify the method with its IPPF code for Initial Consultation.
      $contraception_code = substr($contraception_code, 0, 6) . '110';
      // Xavier confirms that the codes for Cervical Cap (112152010 and 112152011) are
      // an unintended change in pattern, but at this point we have to live with it.
      if ($contraception_code == '112152110') $contraception_code = '112152010';
    }
  }

  // Get details of the last previous instance of this form, if any.
  // * "First contraception at this clinic" should be auto-set to NO
  //   and disabled if that question was answered previously.
  // * "Previous modern contraceptive use" should auto-set to YES
  //   and disabled it was YES previously.
  //
  $js_extra = "";
  $prvrow = sqlQuery("SELECT " .
    "d1.field_value AS newmauser, " .
    "d2.field_value AS pastmodern " .
    "FROM forms AS f " .
    "JOIN form_encounter AS fe ON fe.pid = f.pid AND fe.encounter = f.encounter " .
    "JOIN lbf_data AS d1 ON d1.form_id = f.form_id AND d1.field_id = 'newmauser' " .
    "LEFT JOIN lbf_data AS d2 ON d2.form_id = f.form_id AND d2.field_id = 'pastmodern' " .
    "WHERE f.pid = '$pid' AND " .
    "f.formdir = 'LBFccicon' AND " .
    "f.deleted = 0 AND " .
    "f.form_id != '$formid' AND " .
    "fe.date < '$encdate' " .
    "ORDER BY fe.date DESC, f.form_id DESC LIMIT 1");
  if (!empty($prvrow)) {
    $js_extra .=
      "// There was a previous instance of this form so we know they are not a new MA user.\n" .
      "f.form_newmauser.selectedIndex = 1;\n" .
      "f.form_newmauser.disabled = true;\n";
    if (!empty($prvrow['pastmodern'])) {
      $js_extra .=
        "\n// Past Modern was previously true so must also be true now.\n" .
        "f.form_pastmodern.selectedIndex = 2;\n" .
        "f.form_pastmodern.disabled = true;\n" .
        "pastmodern_autoset = true;\n";
    }
  }

  echo "
var f = document.forms[0];
var sel;

$js_extra

current_method_changed();

sel = f.form_newmethod;
sel.disabled = $newdisabled;
for (var i = 0; i < sel.options.length; ++i) {
 if (sel.options[i].value == '$contraception_code') {
  sel.selectedIndex = i;
  break;
 }
}

sel = f.form_provider;
sel.disabled = $newdisabled;
for (var i = 0; i < sel.options.length; ++i) {
 if (sel.options[i].value == '$contraception_prov') {
  sel.selectedIndex = i;
  break;
 }
}

f.form_curmethod.onchange = function () { current_method_changed(); };
f.form_newmethod.onchange = function () { current_method_changed(); };
f.onsubmit = function () { return mysubmit(); };
";

// Generate alert if method from services is different from a non-empty method in this form.
  $csrow = sqlQuery("SELECT field_value FROM lbf_data WHERE " .
    "form_id = '$formid' AND field_id = 'newmethod'");
  if (!empty($csrow['field_value']) && $csrow['field_value'] != $contraception_code) {
    echo "
alert('" . xl('Method has changed, please save this form to record the new method.') . "');
";
  }
}

// This function generates HTML to go after the Save button.
//
function LBFccicon_additional_buttons() {
  global $formid, $pid, $encounter;
  echo "<input type='submit' name='bn_save' " .
    "value='" . xl('Save and Tally Sheet') . "' />\n";
}

// Custom logic to run at end of save handler.
// In this case we redirect to the Tally Sheet.
//
function LBFccicon_save_exit() {
  if ($_POST['bn_save'] == xl('Save and Tally Sheet')) {
    formJump("{$GLOBALS['rootdir']}/patient_file/encounter/load_form.php?formname=fee_sheet");
    formFooter();
    return true;
  }
  return false;
}

?>

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

  echo "
// Respond to selection of a current method.
// If None, ask if a modern method was used anywhere before.
// Otherwise if new method != current then ask reason for method change.
function current_method_changed() {
 var f = document.forms[0];
 f.form_pastmodern.disabled = true;
 f.form_mcreason.disabled = true;
 if (f.form_curmethod.selectedIndex <= 0) {
  f.form_pastmodern.disabled = false;
 }
 else {
  if (f.form_curmethod.selectedIndex != f.form_newmethod.selectedIndex) {
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
 f.form_newmethod.disabled = false;
 f.form_provider.disabled = false;
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

  $billresult = getBillingByEncounter($pid, $encounter, "*");
  foreach ($billresult as $iter) {
    _LBFccicon_contraception_scan($iter["code_type"], trim($iter["code"]), $iter['provider_id']);
  }

  // If no provider at the line level, use the encounter's default provider.
  if (empty($contraception_prov)) {
    $tmp = sqlQuery("SELECT provider_id FROM form_encounter " .
      "WHERE pid = '$pid' AND encounter = '$encounter' " .
      "ORDER BY id DESC LIMIT 1");
    $contraception_prov = 0 + $tmp['provider_id'];
  }

  // Normalize the IPPF service code to what we use in our list of methods.
  if (!empty($contraception_code)) {
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

  echo "
var f = document.forms[0];
var sel;

current_method_changed();

sel = f.form_newmethod;
sel.disabled = true;
for (var i = 0; i < sel.options.length; ++i) {
 if (sel.options[i].value == '$contraception_code') {
  sel.selectedIndex = i;
  break;
 }
}

sel = f.form_provider;
sel.disabled = true;
for (var i = 0; i < sel.options.length; ++i) {
 if (sel.options[i].value == '$contraception_prov') {
  sel.selectedIndex = i;
  break;
 }
}

f.form_curmethod.onchange = function () { current_method_changed(); };
f.onsubmit = function () { return mysubmit(); };
";
}
?>

<?php
// Copyright (C) 2012 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../globals.php");
require_once($GLOBALS["srcdir"] . "/formdata.inc.php");

$ptid     = formData('ptid'    , 'G') + 0;
$encid    = formData('encid'   , 'G') + 0;
$formname = formData('formname', 'G');
$formid   = formData('formid'  , 'G') + 0;

// The report functions write HTML output, so we have to capture it as a string
// and then massage it into an acceptable JavaScript string literal.
ob_start();

if (substr($formname, 0, 3) == 'LBF') {
  include_once("{$GLOBALS['incdir']}/forms/LBF/report.php");
  lbf_report($ptid, $encid, 2, $formid, $formname);
}
else {
  include_once("{$GLOBALS['incdir']}/forms/$formname/report.php");
  $report_function = $formname . '_report';
  if (!function_exists($report_function)) exit;
  call_user_func($report_function, $ptid, $encid, 2, $formid);
}

// Grab the output data, erase its buffer and stop buffering.
$s = ob_get_contents();
ob_end_clean();

// Zap the data into the floating div as its HTML contents.
$s = str_replace("\r", "", $s);
$s = str_replace("\n", " ", $s);
$s = str_replace("'", "\\'", $s);
echo "ttCallback('$s');\n";
?>

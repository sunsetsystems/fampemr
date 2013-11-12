<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once("../../globals.php");
require_once("$srcdir/forms.inc");
require_once("$srcdir/sql.inc");
require_once("$srcdir/encounter.inc");
require_once("$srcdir/acl.inc");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/formdata.inc.php");

$conn = $GLOBALS['adodb']['db'];

$date             = formData('form_date');
$onset_date       = formData('form_onset_date');
$sensitivity      = formData('form_sensitivity');
$pc_catid         = formData('pc_catid');
$facility_id      = formData('facility_id');
$billing_facility = formData('billing_facility');
$reason           = formData('reason');
$mode             = formData('mode');
$referral_source  = formData('form_referral_source');
$shift            = formData('form_shift');
$voucher_number   = formData('form_voucher_number');

if ($GLOBALS['concurrent_layout'])
  $normalurl = "$rootdir/patient_file/encounter/encounter_top.php";
else
  $normalurl = "$rootdir/patient_file/encounter/patient_encounter.php";

$nexturl = $normalurl;

if ($mode == 'new')
{
  if (empty($_POST['duplicateok']) && $date == date('Y-m-d')) {
    $erow = sqlQuery("SELECT count(*) AS count " .
      "FROM form_encounter AS fe, forms AS f WHERE " .
      "fe.pid = '$pid' AND fe.date = '$date' AND " .
      "f.formdir = 'newpatient' AND f.form_id = fe.id AND f.deleted = 0");
    if ($erow['count'] > 0) {
      die(xl('Save rejected! A visit already exists for today.'));
    }
  }

  $provider_id = $userauthorized ? $_SESSION['authUserID'] : 0;
  $encounter = $conn->GenID("sequences");
  addForm($encounter, "New Patient Encounter",
    sqlInsert("INSERT INTO form_encounter SET " .
      "date = '$date', " .
      "onset_date = '$onset_date', " .
      "reason = '$reason', " .
      "pc_catid = '$pc_catid', " .
      "facility_id = '$facility_id', " .
      "billing_facility = '$billing_facility', " .
      "sensitivity = '$sensitivity', " .
      "referral_source = '$referral_source', " .
      "shift = '$shift', " .
      "voucher_number = '$voucher_number', " .
      "pid = '$pid', " .
      "encounter = '$encounter', " .
      "provider_id = '$provider_id'"),
    "newpatient", $pid, $userauthorized, $date);
}
else if ($mode == 'update')
{
  $id = formData('id');
  $result = sqlQuery("SELECT encounter, sensitivity FROM form_encounter WHERE id = '$id'");
  if ($result['sensitivity'] && !acl_check('sensitivities', $result['sensitivity'])) {
   die("You are not authorized to see this encounter.");
  }
  $encounter = $result['encounter'];
  // See view.php to allow or disallow updates of the encounter date.
  // $datepart = $_POST["day"] ? "date = '$date', " : "";
  $datepart = acl_check('encounters', 'date_a') ? "date = '$date', " : "";
  sqlStatement("UPDATE form_encounter SET " .
    $datepart .
    "onset_date = '$onset_date', " .
    "reason = '$reason', " .
    "pc_catid = '$pc_catid', " .
    "facility_id = '$facility_id', " .
    "billing_facility = '$billing_facility', " .
    "sensitivity = '$sensitivity', " .
    "referral_source = '$referral_source', " .
    "shift = '$shift', " .
    "voucher_number = '$voucher_number' " .
    "WHERE id = '$id'");
}
else {
  die("Unknown mode '$mode'");
}

setencounter($encounter);

// Update the list of issues associated with this encounter.
sqlStatement("DELETE FROM issue_encounter WHERE " .
  "pid = '$pid' AND encounter = '$encounter'");
if (is_array($_POST['issues'])) {
  foreach ($_POST['issues'] as $issue) {
    $query = "INSERT INTO issue_encounter ( " .
      "pid, list_id, encounter " .
      ") VALUES ( " .
      "'$pid', '$issue', '$encounter'" .
    ")";
    sqlStatement($query);
  }
}

// Custom for Chelsea FC.
//
if ($mode == 'new' && $GLOBALS['default_new_encounter_form'] == 'football_injury_audit') {

  // If there are any "football injury" issues (medical problems without
  // "illness" in the title) linked to this encounter, but no encounter linked
  // to such an issue has the injury form in it, then present that form.

  $lres = sqlStatement("SELECT list_id " .
    "FROM issue_encounter, lists WHERE " .
    "issue_encounter.pid = '$pid' AND " .
    "issue_encounter.encounter = '$encounter' AND " .
    "lists.id = issue_encounter.list_id AND " .
    "lists.type = 'medical_problem' AND " .
    "lists.title NOT LIKE '%Illness%'");

  if (mysql_num_rows($lres)) {
    $nexturl = "$rootdir/patient_file/encounter/load_form.php?formname=" .
      $GLOBALS['default_new_encounter_form'];
    while ($lrow = sqlFetchArray($lres)) {
      $frow = sqlQuery("SELECT count(*) AS count " .
         "FROM issue_encounter, forms WHERE " .
         "issue_encounter.list_id = '" . $lrow['list_id'] . "' AND " .
         "forms.pid = issue_encounter.pid AND " .
         "forms.encounter = issue_encounter.encounter AND " .
         "forms.formdir = '" . $GLOBALS['default_new_encounter_form'] . "'");
      if ($frow['count']) $nexturl = $normalurl;
    }
  }
}
?>
<html>
<body>
<script language="Javascript">
<?php if ($GLOBALS['concurrent_layout'] && $mode == 'new') { ?>
 parent.left_nav.setEncounter(<?php echo "'" . oeFormatShortDate($date) . "', $encounter, window.name"; ?>);
 parent.left_nav.setRadio(window.name, 'enc');
<?php } ?>
 top.restoreSession();
 window.location="<?php echo $nexturl; ?>";
</script>

</body>
</html>

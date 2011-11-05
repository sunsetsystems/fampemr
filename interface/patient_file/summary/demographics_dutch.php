<?php
 require_once("../../globals.php");
 require_once("$srcdir/patient.inc");
 require_once("$srcdir/acl.inc");
 require_once("$srcdir/classes/Address.class.php");
 require_once("$srcdir/classes/InsuranceCompany.class.php");

 if ($GLOBALS['concurrent_layout'] && $_GET['set_pid']) {
  include_once("$srcdir/pid.inc");
  setpid($_GET['set_pid']);
 }

function print_as_money($money) {
	preg_match("/(\d*)\.?(\d*)/",$money,$moneymatches);
	$tmp = wordwrap(strrev($moneymatches[1]),3,",",1);
	$ccheck = strrev($tmp);
	if ($ccheck[0] == ",") {
		$tmp = substr($ccheck,1,strlen($ccheck)-1);
	}
	if ($moneymatches[2] != "") {
		return "$ " . strrev($tmp) . "." . $moneymatches[2];
	} else {
		return "$ " . strrev($tmp);
	}
}

/****
function get_billing_note($pid) {
	$conn = $GLOBALS['adodb']['db'];
	$billing_note = "";
	$sql = "select genericname2, genericval2 " .
		"from patient_data where pid = '$pid' limit 1";
	$resnote = $conn->Execute($sql);
	if($resnote && !$resnote->EOF && $resnote->fields['genericname2'] == 'Billing') {
		$billing_note = $resnote->fields['genericval2'];
	}
	return $billing_note;
}
****/

function get_patient_balance($pid) {
	require_once($GLOBALS['fileroot'] . "/library/classes/WSWrapper.class.php");
	$conn = $GLOBALS['adodb']['db'];
	$customer_info['id'] = 0;
	$sql = "SELECT foreign_id FROM integration_mapping AS im " .
		"LEFT JOIN patient_data AS pd ON im.local_id = pd.id WHERE " .
		"pd.pid = '" . $pid . "' AND im.local_table = 'patient_data' AND " .
		"im.foreign_table = 'customer'";
	$result = $conn->Execute($sql);
	if($result && !$result->EOF) {
		$customer_info['id'] = $result->fields['foreign_id'];
	}
	$function['ezybiz.customer_balance'] = array(new xmlrpcval($customer_info,"struct"));
	$ws = new WSWrapper($function);
	if(is_numeric($ws->value)) {
		return sprintf('%01.2f', $ws->value);
	}
	return '';
}

?>
<html>

<head>
<link rel=stylesheet href="<?echo $css_header;?>" type="text/css">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script language="JavaScript">

 function oldEvt(eventid) {
  dlgopen('../../main/calendar/add_edit_event.php?eid=' + eventid, '_blank', 550, 270);
 }

 function refreshme() {
  location.reload();
 }

 // Process click on Delete link.
 function deleteme() {
  dlgopen('../deleter.php?patient=<?php echo $pid ?>', '_blank', 500, 450);
  return false;
 }

 // Called by the deleteme.php window on a successful delete.
 function imdeleted() {
<?php if ($GLOBALS['concurrent_layout']) { ?>
  parent.left_nav.clearPatient();
<?php } else { ?>
  top.location.href = '../main/main_screen.php';
<?php } ?>
 }

</script>
</head>

<body <?echo $top_bg_line;?> topmargin=0 rightmargin=0 leftmargin=2 bottommargin=0 marginwidth=2 marginheight=0>

<?php
 $result = getPatientData($pid, "*, DATE_FORMAT(DOB,'%Y-%m-%d') as DOB_YMD"); 
// DBC Dutch System
    $result_NL = getPatientDataNL($pid);
    // clean the sessions var for the demographics_full
    $_SESSION['errormsg'] = '';
// EOS DBC
 $result2 = getEmployerData($pid);

 $thisauth = acl_check('patients', 'demo');
 if ($thisauth) {
  if ($result['squad'] && ! acl_check('squads', $result['squad']))
   $thisauth = 0;
 }

 if (!$thisauth) {
  echo "<p>(".xl('Demographics not authorized').")</p>\n";
  echo "</body>\n</html>\n";
  exit();
 }

 if ($thisauth == 'write') {
    // DUTCH SWITCH FOR DEMOGRAPHICS FULL
    $demovar = ( $GLOBALS['dutchpc'] ) ? 'demographics_full_dutch.php' : 'demographics_full.php';

  echo "<p><a href='$demovar'";
   if (! $GLOBALS['concurrent_layout']) echo " target='Main'";
   echo "><font class='title'>" . xl('Demographics') . "</font>" .
   "<font class='more'>$tmore</font></a>";
  if (acl_check('admin', 'super')) {
   echo "&nbsp;&nbsp;<a href='' onclick='return deleteme()'>" .
    "<font class='more' style='color:red'>(".xl('Delete').")</font></a>";
  }
  echo "</p>\n";
 }

// Get the document ID of the patient ID card if access to it is wanted here.
$document_id = 0;
if ($GLOBALS['patient_id_category_name']) {
  $tmp = sqlQuery("SELECT d.id, d.date, d.url FROM " .
    "documents AS d, categories_to_documents AS cd, categories AS c " .
    "WHERE d.foreign_id = $pid " .
    "AND cd.document_id = d.id " .
    "AND c.id = cd.category_id " .
    "AND c.name LIKE '" . $GLOBALS['patient_id_category_name'] . "' " .
    "ORDER BY d.date DESC LIMIT 1");
  if ($tmp) $document_id = $tmp['id'];
}
?>

<table border="0" width="100%">
 <tr>
  <td align="left" valign="top">
   <table border='0' cellpadding='0' width='100%'>
    <tr>
     <td valign='top' width='33%'>
      <span class='bold'><?php xl('Name','e'); ?>: </span>
      <span class='text'>
<?php
if ($document_id) echo "<a href='" . $web_root . "/controller.php?document&retrieve" .
  "&patient_id=$pid&document_id=$document_id' style='color:#00cc00'>";
if (!$GLOBALS['omit_employers']) echo $result['title'] . ' ';

// DBC
if ( $GLOBALS['dutchpc'] ) {
    // we dont use middle name, just put the dutch prefix here
    echo sutf8(dutch_name($pid));
} else {
    echo $result['fname'] . ' ' . $result['mname'] . ' ' . $result['lname'];
}
// EOS DBC

if ($document_id) echo "</a>";
?>
      </span><br>
      <span class='bold'><? xl('Number','e'); ?>: </span><span class='text'><?echo $result{"pubpid"}?></span>
     </td>
     <td valign='top' width='33%'>
<?
 if ($result{"DOB"} && $result{"DOB"} != "0000-00-00") {
?>
      <span class='bold'><? xl('DOB','e'); ?>: </span>
      <span class='text'>
<?
  echo $result{"DOB"};
 }
?>
      </span><br>
<?php if ($result{"ss"} != "") { ?>
      <span class='bold'><?php xl('S.S.','e'); ?>: </span>
<?php } ?>
      <span class='text'><?php echo $result{"ss"} ?></span>
     </td>
     <td valign='top' width='34%'>
<?php if ($result{"sex"} != "") { ?>
      <span class='bold'><?php xl('Sex','e'); ?>: </span>
<?php } ?>
      <span class='text'><?php echo $result{"sex"} ?></span>
     </td>
    </tr>

<?php 
// DBC Dutch SYSTEM
if ( $GLOBALS['dutchpc'] ) {
  // verify if the current patient has a facility associated
 
  echo '<tr><td>';
  echo '<span class="text"><b>ID1250</b> ' .get_id('id1250', $pid). '</span>';  
  echo '</td></tr>';
}
?>

    <tr>
     <td valign='top'>
<?php if (($result{"street"} != "") || ($result{"city"} != "") || ($result{"state"} != "") || ($result{"country_code"} != "") || ($result{"postal_code"} != "")) {?>
      <span class='bold'><? xl('Address','e'); ?>: </span>
<?php } ?>
      <br><span class='text'><?echo $result{"street"}?><br><?echo $result{"city"}?><?if($result{"city"} != ""){echo ", ";}?><?echo $result{"state"};?>
<? if($result{"country_code"} != ""){ echo ", "; }?><?echo country_code($result{"country_code"})?>
<?echo " ";
echo $result{"postal_code"}?>
      </span>
     </td>
     <td valign='top'>
<?
	if (	($result{"contact_relationship"} != "") ||
		($result{"phone_contact"} != "") ||
		($result{"phone_home"} != "") ||
		($result{"phone_biz"} != "") ||
		($result{"email"} != "")  ||
		($result{"phone_cell"} != "")    ){
?>
      <span class='bold'><? xl('Emergency Contact','e'); ?>: </span><?}?><span class='text'><?echo $result{"contact_relationship"}?><?echo " "?>
<?
	if ($result{"phone_contact"} != "") {
		echo " " . $result{"phone_contact"};
	}
	if ($result{"phone_home"} != "") {
		echo "<br><span class='bold'>Home:</span> ";
		echo $result{"phone_home"};
	}
	if ($result{"phone_biz"} != "") {
		echo "<br><span class='bold'>Work:</span> ";
		echo $result{"phone_biz"};
	}
	if ($result{"phone_cell"} != "") {
		echo "<br><span class='bold'>Mobile:</span> ";
		echo $result{"phone_cell"};
	}
	if ($result{"email"} != "") {
		echo "<br><span class='bold'>".xl('Email').": </span>";
		echo '<a class=link_submit href="mailto:' . $result{"email"} . '">' . $result{"email"} . '</a>';
	}
?>
     </td>
     <td valign='top'>
<?
	if ($result{"status"} != "") {
		echo "<span class='bold'>".xl('Marital Status').": </span>";
		echo "<span class='text'>" .  $result{"status"} . "</span>";
	}
?>
     </td>
    </tr>

<?php if (!$GLOBALS['athletic_team']) { ?>
    <tr>
     <td colspan='3' valign='top'>
	<?php
		$opt_out = ($result{"hipaa_mail"} == 'YES') ? 'ALLOWS' : 'DOES NOT ALLOW';
		echo "<span class='text'>Patient $opt_out Mailed Information </span>";
	?>
     </td>
    </tr>
    <tr>
     <td colspan='2' valign='top'>
	<?php
		$opt_out = ($result{"hipaa_voice"} == 'YES') ? 'ALLOWS' : 'DOES NOT ALLOW';
		echo "<span class='text'>Patient $opt_out Voice Messages </span>";
	?>
     </td>
     <td colspan='1' valign='top'>
<?php
	echo "<span class='bold'><font color='#ee6600'>Balance Due: $" .
		get_patient_balance($pid) . "</font>";
	if ($result['genericname2'] == 'Billing')
		echo "<br>" . xl('Billing Note') . ":";
	echo "</span>";
?>
     </td>
    </tr>
    <tr>
     <td colspan='2' valign='top'>
<?php
		$opt_out = ($result{"hipaa_notice"} == 'YES') ? 'RECEIVED' : 'DID NOT RECEIVE';
		echo "<span class='text'>Patient $opt_out Notice Information </span>";
?>
     </td>
     <td colspan='1' valign='top'>
<?php
	if ($result['genericname2'] == 'Billing')
		echo "<span class='bold'><font color='red'>" .
		$result['genericval2'] . "</font></span>";
?>
     </td>
    </tr>
    <tr>
     <td colspan='3' valign='top'>
	<?php
		if ( $result["hipaa_message"] == "" ) {
			echo "<span class='text'><b>Leave a message with :</b> " .
				$result{"fname"} . " " . $result{"mname"} . " " .
				$result{"lname"} . "</span>";
		}
		else {
			echo "<span class='text'><b>Leave a message with :</b> " .
				$result{"hipaa_message"} . "</span>";
		}
	?>
     </td>
    </tr>

<?php } else { ?>
    <tr>
     <td colspan='3' valign='top'>
      &nbsp;
     </td>
    </tr>
<?php } ?>

<?php if ($GLOBALS['omit_employers']) { ?>

    <tr>
     <td valign='top' colspan='2'>
      <table>
       <tr>
        <td><span class='bold'>Listed Family Members:</span></td>
        <td>&nbsp;</td>
       </tr>
       <tr>
        <td><?php if ($result{"genericname1"} != "") { ?><span class='text'>&nbsp;&nbsp;&nbsp;<?=$result{"genericname1"}?></span><?php } ?></td>
        <td><?php if ($result{"genericval1"} != "") { ?><span class='text'>&nbsp;&nbsp;&nbsp;<?=$result{"genericval1"}?></span><?php } ?></td>
       </tr>
       <tr>
        <td><?php if ($result{"genericname2"} != "") { ?><span class='text'>&nbsp;&nbsp;&nbsp;<?=$result{"genericname2"}?></span><?php } ?></td>
        <td><?php if ($result{"genericval2"} != "") { ?><span class='text'>&nbsp;&nbsp;&nbsp;<?=$result{"genericval2"}?></span><?php } ?></td>
       </tr>
      </table>
     </td>
     <td valign='top'></td>
    </tr>

<?php } else { ///// end omit_employers ///// ?>

    <tr>
     <td valign='top'>
<?php if ($result{"occupation"} != "") { ?>
      <span class='bold'><?php xl('Occupation','e'); ?>: </span><span class='text'><?echo $result{"occupation"}?></span><br>
<?php } ?>
<?php if ($result2{"name"} != "") { ?>
      <span class='bold'><?php xl('Employer','e'); ?>: </span><span class='text'><?php echo $result2{"name"} ?></span>
<?php } ?>
     </td>
     <td valign='top'>
<?php if (($result2{"street"} != "") || ($result2{"city"} != "") || ($result2{"state"} != "") || ($result2{"country"} != "") || ($result2{"postal_code"} != "")) { ?>
      <span class='bold'><? xl('Employer Address','e'); ?>:</span>
<?php } ?>
      <br>
      <span class='text'>
<?php echo $result2{"street"}?><br><?php echo $result2{"city"} ?><?php if($result2{"city"} != "") { echo ", "; } ?><?php echo $result2{"state"} ?>
<?php if($result2{"country"} != "") { echo ", "; } echo $result2{"country"} ?>
<?php if($result2{"postal_code"} != "") {echo " "; } ?>
<?php echo $result2{"postal_code"} ?>
      </span>
     </td>
     <td valign='top'>
<?php
 // This stuff only applies to athletic team use of OpenEMR:
 if ($GLOBALS['athletic_team']) {
  //                  blue       dk green   yellow     red        orange
  $fitcolors = array('#6677ff', '#00cc00', '#ffff00', '#ff3333', '#ff8800', '#ffeecc', '#ffccaa');
  $fitcolor = $fitcolors[0];
  $fitness = $_POST['form_fitness'];
  if ($fitness) {
   sqlStatement("UPDATE patient_data SET fitness = '$fitness' WHERE pid = '$pid'");
  } else {
   $fitness = $result['fitness'];
   if (! $fitness) $fitness = 1;
  }
  $fitcolor = $fitcolors[$fitness - 1];
?>
      // larry :: Dutch DBC insert
      <?php if( $GLOBALS['dutchpc'] ) 
      { ?>
      <form method='post' action='demographics_dutch.php'>
      <?php } else
      { ?>
      <form method='post' action='demographics.php'>
      <?php } ?>
      // larry :: end DBC insert
      
      <span class='bold'><? xl('Fitness to Play','e'); ?>:</span><br>
      <select name='form_fitness' onchange='document.forms[0].submit()' style='background-color:<? echo $fitcolor ?>'>
       <option value='1'<? if ($fitness == 1) echo ' selected' ?>><? xl('Full Play','e'); ?></option>
       <option value='2'<? if ($fitness == 2) echo ' selected' ?>><? xl('Full Training','e'); ?></option>
       <option value='3'<? if ($fitness == 3) echo ' selected' ?>><? xl('Restricted Training','e'); ?></option>
       <option value='4'<? if ($fitness == 4) echo ' selected' ?>><? xl('Injured Out','e'); ?></option>
       <option value='5'<? if ($fitness == 5) echo ' selected' ?>><? xl('Rehabilitation','e'); ?></option>
       <option value='6'<? if ($fitness == 6) echo ' selected' ?>><? xl('Illness','e'); ?></option>
       <option value='7'<? if ($fitness == 7) echo ' selected' ?>><? xl('International Duty','e'); ?></option>
      </select>
      </form>
<?php } // end athletic team ?>
     </td>
    </tr>
    <tr>
     <td valign='top'>
<?php if (! $GLOBALS['athletic_team']) { ?>
<?php if ($result{"ethnoracial"} != "")  { ?><span class='bold'><? xl('Race/Ethnicity','e'); ?>: </span><span class='text'><?echo $result{"ethnoracial"};?></span><br><? } ?>
<?php if ($result{"language"} != "")     { ?><span class='bold'><? xl('Language','e'); ?>: </span><span class='text'><?echo ucfirst($result{"language"});?></span><br><? } ?>
<?php if ($result{"interpretter"} != "") { ?><span class='bold'><? xl('Interpreter','e'); ?>: </span><span class='text'><?echo $result{"interpretter"};?></span><br><? } ?>
<?php if ($result{"family_size"} != "")  { ?><span class='bold'><? xl('Family Size','e'); ?>: </span><span class='text'><?echo $result{"family_size"};?></span><br><? } ?>
<?php } ?>
     </td>
     <td valign='top'>
<?php if (! $GLOBALS['athletic_team']) { ?>
<?php if ($result{"financial_review"} != "0000-00-00 00:00:00") {?><span class='bold'><? xl('Financial Review Date','e'); ?>: </span><span class='text'><?echo date("n/j/Y",strtotime($result{"financial_review"}));?></span><br><?}?>
<?php if ($result{"monthly_income"} != "") {?><span class='bold'><? xl('Monthly Income','e'); ?>: </span><span class='text'><?echo print_as_money($result{"monthly_income"});?></span><br><?}?>
<?php if ($result{"migrantseasonal"} != "") {?><span class='bold'><? xl('Migrant/Seasonal','e'); ?>: </span><span class='text'><?echo $result{"migrantseasonal"};?></span><br><?}?>
<?php if ($result{"homeless"} != "") {?><span class='bold'><? xl('Homeless, etc','e'); ?>.: </span><span class='text'><?echo $result{"homeless"};?></span><br><?}?>
<?php } ?>
     </td>
     <td valign='top'>
      <table>
       <tr>
        <td><? if ($result{"genericname1"} != "") {?><span class='bold'><?=$result{"genericname1"}?></span>:<?}?> </td>
        <td><? if ($result{"genericval1"} != "") {?><span class='text'><?=$result{"genericval1"}?></span><?}?></td>
       </tr>
       <tr>
        <td><? if ($result{"genericname2"} != "") {?><span class='bold'><?=$result{"genericname2"}?></span>:<?}?> </td>
        <td><? if ($result{"genericval2"} != "") {?><span class='text'><?=$result{"genericval2"}?></span><?}?></td>
       </tr>
      </table>
     </td>
    </tr>

<?php } ///// end not omit_employers ///// ?>

<?php
//////////////////////////////////REFERRAL SECTION
if ($result{"referrer"} != "" || $result{"referrerID"} != "")
{
?>
    <tr>
     <td valign='top' colspan='3'>
      <span class='bold'><? xl('Primary Provider','e'); ?>: </span><span class='text'><?=getProviderName($result['providerID'])?></span><br>
      <!--<span class='bold'>Primary Provider ID: </span><span class='text'><?=$result{"referrerID"}?></span>-->
     </td>
    </tr>
<?php
}

/////////////////////////////////INSURANCE SECTION
$result3 = getInsuranceData($pid, "primary");
if ($result3{"provider"}) {
  $icobj = new InsuranceCompany($result3['provider']);
  $adobj = $icobj->get_address();
?>
    <tr>
     <td valign='top'>
      <span class='bold'><?php xl('Primary Insurance','e'); ?>:</span><br><span class='text'>
<?php
  if (trim($result3['provider_name'])) {
    echo $result3['provider_name'] . '<br>';
    if (trim($adobj->get_line1())) {
      echo $adobj->get_line1() . '<br>';
      echo $adobj->get_city() . ', ' . $adobj->get_state() . ' ' . $adobj->get_zip();
    }
  } else {
    echo "<font color='red'><b>Unassigned</b></font>";
  }
?>
      </span><br>
      <span class='text'><? xl('Policy Number','e'); ?>: <?echo $result3{"policy_number"}?><br>
      Plan Name: <?=$result3{"plan_name"}?><br>
      Group Number: <?echo $result3{"group_number"}?></span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber','e'); ?>: </span><br><span class='text'><?=$result3{"subscriber_fname"}?> <?=$result3{"subscriber_mname"}?> <?=$result3{"subscriber_lname"}?> <?if ($result3{"subscriber_relationship"} != "") {echo "(".$result3{"subscriber_relationship"}.")";}?><br>
      S.S.: <?echo $result3{"subscriber_ss"}?><br>
      <?php xl('D.O.B.','e'); ?>: <?if ($result3{"subscriber_DOB"} != "0000-00-00 00:00:00") {echo $result3{"subscriber_DOB"};}?><br>
      Phone: <? echo $result3{"subscriber_phone"}?>
      </span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber Address','e'); ?>: </span><br><span class='text'><?echo $result3{"subscriber_street"}?><br><?echo $result3{"subscriber_city"}?><?if($result3{"subscriber_state"} != ""){echo ", ";}?><?echo $result3{"subscriber_state"}?><?if($result3{"subscriber_country"} != ""){echo ", ";}?><?echo $result3{"subscriber_country"}?> <?echo " ".$result3{"subscriber_postal_code"}?></span>

<?php if (trim($result3['subscriber_employer'])) { ?>
      <br><span class='bold'><?php xl('Subscriber Employer','e'); ?>: </span><br>
      <span class='text'><?php echo $result3{"subscriber_employer"}?><br>
      <?php echo $result3{"subscriber_employer_street"}?><br>
      <?php echo $result3{"subscriber_employer_city"}?>
      <?php if($result3{"subscriber_employer_city"} != ""){echo ", ";} echo $result3{"subscriber_employer_state"}?>
      <?php if($result3{"subscriber_employer_country"} != ""){echo ", ";} echo $result3{"subscriber_employer_country"}?>
      <?php echo " ".$result3{"subscriber_employer_postal_code"}?>
      </span>
<?php } ?>

     </td>
    </tr>
    <tr>
     <td><? if ($result3{"copay"} != "") {?><span class='bold'><? xl('CoPay','e'); ?>: </span><span class='text'><?=$result3{"copay"}?></span><?}?></td>
     <td valign='top'></td>
     <td valign='top'></td>
   </tr>
<? } ?>
<?
$result4 = getInsuranceData($pid, "secondary");
if ($result4{"provider"} != "") {
  $icobj = new InsuranceCompany($result4['provider']);
  $adobj = $icobj->get_address();
?>
    <tr>
     <td valign='top'>
      <span class='bold'><? xl('Secondary Insurance','e'); ?>:</span><br><span class='text'>
<?php
  if (trim($result4['provider_name'])) {
    echo $result4['provider_name'] . '<br>';
    if (trim($adobj->get_line1())) {
      echo $adobj->get_line1() . '<br>';
      echo $adobj->get_city() . ', ' . $adobj->get_state() . ' ' . $adobj->get_zip();
    }
  } else {
    echo "<font color='red'><b>Unassigned</b></font>";
  }
?>
      </span><br>
      <span class='text'><? xl('Policy Number','e'); ?>: <?echo $result4{"policy_number"}?><br>
      Plan Name: <?=$result4{"plan_name"}?><br>
      Group Number: <?echo $result4{"group_number"}?></span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber','e'); ?>: </span><br><span class='text'><?=$result4{"subscriber_fname"}?> <?=$result4{"subscriber_mname"}?> <?=$result4{"subscriber_lname"}?> <?if ($result4{"subscriber_relationship"} != "") {echo "(".$result4{"subscriber_relationship"}.")";}?><br>
      S.S.: <?echo $result4{"subscriber_ss"}?> <? xl('D.O.B.','e'); ?>: <?if ($result4{"subscriber_DOB"} != "0000-00-00 00:00:00") {echo $result4{"subscriber_DOB"};}?><br>
      Phone: <? echo $result4{"subscriber_phone"}?>
      </span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber Address','e'); ?>: </span><br><span class='text'><?echo $result4{"subscriber_street"}?><br><?echo $result4{"subscriber_city"}?><?if($result4{"subscriber_state"} != ""){echo ", ";}?><?echo $result4{"subscriber_state"}?><?if($result4{"subscriber_country"} != ""){echo ", ";}?><?echo $result4{"subscriber_country"}?> <?echo " ".$result4{"subscriber_postal_code"}?></span>

<?php if (trim($result4['subscriber_employer'])) { ?>
      <br><span class='bold'><?php xl('Subscriber Employer','e'); ?>: </span><br>
      <span class='text'><?php echo $result4{"subscriber_employer"}?><br>
      <?php echo $result4{"subscriber_employer_street"}?><br>
      <?php echo $result4{"subscriber_employer_city"}?>
      <?php if($result4{"subscriber_employer_city"} != ""){echo ", ";} echo $result4{"subscriber_employer_state"}?>
      <?php if($result4{"subscriber_employer_country"} != ""){echo ", ";} echo $result4{"subscriber_employer_country"}?>
      <?php echo " ".$result4{"subscriber_employer_postal_code"}?>
      </span>
<?php } ?>

     </td>
    </tr>
    <tr>
     <td>
      <? if ($result4{"copay"} != "") {?><span class='bold'><? xl('CoPay','e'); ?>: </span><span class='text'><?=$result4{"copay"}?></span><?}?>
     </td>
     <td valign='top'></td>
     <td valign='top'></td>
    </tr>
<? } ?>
<?
$result5 = getInsuranceData($pid, "tertiary");
if ($result5{"provider"}) {
  $icobj = new InsuranceCompany($result5['provider']);
  $adobj = $icobj->get_address();
?>
    <tr>
     <td valign='top'>
      <span class='bold'><? xl('Tertiary Insurance','e'); ?>:</span><br><span class='text'>
<?php
  if (trim($result5['provider_name'])) {
    echo $result5['provider_name'] . '<br>';
    if (trim($adobj->get_line1())) {
      echo $adobj->get_line1() . '<br>';
      echo $adobj->get_city() . ', ' . $adobj->get_state() . ' ' . $adobj->get_zip();
    }
  } else {
    echo "<font color='red'><b>Unassigned</b></font>";
  }
?>
      </span><br>
      <span class='text'><? xl('Policy Number','e'); ?>: <?echo $result5{"policy_number"}?><br>
      Plan Name: <?=$result5{"plan_name"}?><br>
      Group Number: <?echo $result5{"group_number"}?></span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber','e'); ?>: </span><br><span class='text'><?=$result5{"subscriber_fname"}?> <?=$result5{"subscriber_mname"}?> <?=$result5{"subscriber_lname"}?> <?if ($result5{"subscriber_relationship"} != "") {echo "(".$result5{"subscriber_relationship"}.")";}?><br>
      S.S.: <?echo $result5{"subscriber_ss"}?> <? xl('D.O.B.','e'); ?>: <?if ($result5{"subscriber_DOB"} != "0000-00-00 00:00:00") {echo $result5{"subscriber_DOB"};}?><br>
      Phone: <? echo $result5{"subscriber_phone"}?>
      </span>
     </td>
     <td valign='top'>
      <span class='bold'><? xl('Subscriber Address','e'); ?>: </span><br><span class='text'><?echo $result5{"subscriber_street"}?><br><?echo $result5{"subscriber_city"}?><?if($result5{"subscriber_state"} != ""){echo ", ";}?><?echo $result5{"subscriber_state"}?><?if($result5{"subscriber_country"} != ""){echo ", ";}?><?echo $result5{"subscriber_country"}?> <?echo " ".$result5{"subscriber_postal_code"}?></span>

<?php if (trim($result5['subscriber_employer'])) { ?>
      <br><span class='bold'><?php xl('Subscriber Employer','e'); ?>: </span><br>
      <span class='text'><?php echo $result5{"subscriber_employer"}?><br>
      <?php echo $result5{"subscriber_employer_street"}?><br>
      <?php echo $result5{"subscriber_employer_city"}?>
      <?php if($result5{"subscriber_employer_city"} != ""){echo ", ";} echo $result5{"subscriber_employer_state"}?>
      <?php if($result5{"subscriber_employer_country"} != ""){echo ", ";} echo $result5{"subscriber_employer_country"}?>
      <?php echo " ".$result5{"subscriber_employer_postal_code"}?>
      </span>
<?php } ?>

     </td>
    </tr>
    <tr>
     <td>
      <? if ($result5{"copay"} != "") {?><span class='bold'><? xl('CoPay','e'); ?>: </span><span class='text'><?=$result5{"copay"}?></span><?}?>
     </td>
     <td valign='top'></td>
     <td valign='top'></td>
    </tr>
<?
}
?>
   </table>
  </td>
  <td valign="top" class="text">
<?php
if (isset($pid)) {
 /*$query = "SELECT e.pc_eid, e.pc_aid, e.pc_title, e.pc_eventDate, " .
  "e.pc_startTime, u.fname, u.lname, u.mname " .
  "FROM openemr_postcalendar_events AS e, users AS u WHERE " .
  "e.pc_pid = '$pid' AND e.pc_eventDate >= CURRENT_DATE AND " .
  "u.id = e.pc_aid " .
  "ORDER BY e.pc_eventDate, e.pc_startTime";*/
  $query = "SELECT e.pc_eid, e.pc_aid, e.pc_title, e.pc_eventDate, " .
  "e.pc_startTime, u.fname, u.lname, u.mname " .
  "FROM openemr_postcalendar_events AS e, users AS u WHERE " .
  "e.pc_pid = '$pid' AND e.pc_eventDate >= '2007-01-01' AND " .
  "u.id = e.pc_aid " .
  "ORDER BY e.pc_eventDate, e.pc_startTime";
 $res = sqlStatement($query);
 while($row = sqlFetchArray($res)) {
  $dayname = date("l", strtotime($row['pc_eventDate']));
  $dispampm = "am";
  $disphour = substr($row['pc_startTime'], 0, 2) + 0;
  $dispmin  = substr($row['pc_startTime'], 3, 2);
  if ($disphour >= 12) {
   $dispampm = "pm";
   if ($disphour > 12) $disphour -= 12;
  }
  echo "<a href='javascript:oldEvt(" . $row['pc_eid'] .
       ")'><b>$dayname " . $row['pc_eventDate'] . "</b><br>";
  echo "$disphour:$dispmin $dispampm " . $row['pc_title'] . "<br>\n";
  echo $row['fname'] . " " . $row['lname'] . "</a><br>&nbsp;<br>\n";
 }
}
?>
  </td>
 </tr>
</table>

<?php if ($GLOBALS['concurrent_layout'] && $_GET['set_pid']) { ?>
<script language='JavaScript'>
 parent.left_nav.setPatient(<?php echo "'" . addslashes($result['fname']) . " " . addslashes($result['lname']) . "',$pid,'" . addslashes($result['pubpid']) . "','', ' DOB: ".$result['DOB_YMD']." Age: ".getPatientAge($result['DOB_YMD'])."'"; ?>);
 parent.left_nav.setRadio(window.name, 'dem');
<?php if (!$_GET['is_new']) { // if new pt, do not load other frame ?>
 var othername = (window.name == 'RTop') ? 'RBot' : 'RTop';
 parent.left_nav.setRadio(othername, 'sum');
 parent.left_nav.loadFrame(othername, 'patient_file/summary/summary_bottom.php');
<?php } ?>
</script>
<?php } ?>

</body>
</html>

<?php
require_once("language.inc.php");


if ($_POST['Submit']==xl('Add')){
	//validate	
	$myconst=$_POST['constant_name'];
	$sql="SELECT * FROM lang_constants WHERE constant_name='".$_POST['constant_name']."' limit 1" ;
	$res=SqlQuery($sql);
	if ( $res ) {
		echo xl('Data Alike is already in database, please change constant name','','','<br>');
		$err='y';
	}
	if ($err=='y'){
		$val_constant=$myconst;
	} else {
		$sql="INSERT INTO lang_constants SET constant_name='".$myconst."'"; 
		SqlStatement ($sql);
		echo xl('Constant','','',' ') . $myconst  . xl('added','',' ','<br>');
	}
	


// echo "$sql here ";
}

?>

<TABLE>
<FORM name="cons_form" METHOD=POST ACTION="?m=constant">
<TR>
	<TD><? xl ('constant name','e'); ?></TD>
	<TD><INPUT TYPE="text" NAME="constant_name" size="100" value="<? echo $val_constant; ?>"></TD>
</TR>
<TR>
	<TD></TD>
	<TD><INPUT TYPE="submit" name="Submit" value="<?php xl('Add','e'); ?>"></TD>
</TR>
</FORM>
</TABLE>
<?php xl('Please Note: constants are case sensitive and any string is allowed.','e'); ?>

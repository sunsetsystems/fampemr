<?php
include_once("../../globals.php");
?>

<HTML>
<head>
<?php html_header_show();?>
<TITLE><?php xl('Patient Summary','e'); ?>
</TITLE>
</HEAD>
<frameset rows="50%,50%" cols="*">
  <frame src="add_transaction.php" name="New Transaction" scrolling="auto">
  <frame src="transactions.php" name="Transactions" scrolling="auto">	
  
</frameset>

<noframes><body bgcolor="#FFFFFF">
Frames support required
</body></noframes>

</HTML>

<?php
// Copyright (C) 2010-2011 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

require_once "version.php";

// Make this true for debugging, false for production.
$DEBUG = false;

$webserver_root = dirname(__FILE__);
if (stripos(PHP_OS,'WIN') === 0)
  $webserver_root = str_replace("\\","/",$webserver_root); 
$OE_SITES_BASE = "$webserver_root/sites";

function sqlQuery($statement) {
  $row = @mysql_fetch_array(mysql_query($statement), MYSQL_ASSOC);
  return $row;
}

// Recursively delete a directory. Symbolic links are NOT followed.
function recurse_delete($src) {
  global $DEBUG;
  // Avoiding readdir() as it is unclear whether deleting entries while reading
  // could mess it up. Note scandir() requires php 5+.
  if (strlen($src) < 6) die("Trying to delete '$src' seems wrong, directory removal aborted.");
  $names = scandir($src);
  if ($names === FALSE) die("Cannot scan directory '$src', directory removal aborted.");
  foreach ($names as $name) {
    if ($name != '.' && $name != '..') {
      $path = $src . '/' . $name;
      if (is_dir($path) && !is_link($path)) {
        recurse_delete($path);
      }
      else {
        if ($DEBUG) {
          echo "Would unlink '$path'.<br />\n";
        }
        else {
          if (!unlink($path)) die("Cannot unlink '$path', directory removal aborted.");
        }
      }
    }
  }
  if ($DEBUG) {
    echo "Would rmdir '$src'.<br />\n";
  }
  else {
    if (!rmdir($src)) die("Cannot remove directory '$src', directory removal aborted.");
  }
}
?>
<html>
<head>
<title>OpenEMR Site Administration</title>
<link rel='STYLESHEET' href='interface/themes/style_sky_blue.css'>
<style>
tr.head   { font-size:10pt; background-color:#cccccc; text-align:center; font-weight:bold; }
tr.detail { font-size:10pt; }
a, a:visited, a:hover { color:#0000cc; text-decoration:none; }
</style>

<script language="javascript">

function add_clicked() {
 document.location.href = "setup.php";
}

function go_clicked() {
 var f = document.forms[0];
 var a = f.form_site;
 // If only one radio button then the following line is necessary.
 if (typeof a.length === 'undefined') a = new Array(a);
 var i = 0;
 for (i = 0; i < a.length; ++i) {
  if (a[i].checked) {
   if (f.form_action.value == 'login') {
    document.location.href = "interface/login/login_frame.php?site=" + a[i].value;
   }
   else if (f.form_action.value == 'upgrade') {
    document.location.href = "sql_upgrade.php?site=" + a[i].value;
   }
   else if (f.form_action.value == 'delete') {
    f.submit();
   }
   return;
  }
 }
 alert("No site selected!");
}

</script>

</head>
<body>
<form method='post' action='admin.php'>
<center>

<?php
if (!empty($_POST['form_site'])) {
  $siteid = $_POST['form_site'];
  if ($siteid == 'default') die("Deleting the default site is not allowed.");
  if (!empty($_POST['form_action']) && $_POST['form_action'] == 'delete') {
    echo "<p>Are you sure you want to delete site '$siteid'? If yes, please " .
      "provide the name and password for the MySQL root account.</p>\n";
    echo "<input type='hidden' name='form_site' value='$siteid' />\n";
    echo "<p>MySQL root account name: ";
    echo "<input type='text' name='form_rootname' value='root' />";
    echo " Password: ";
    echo "<input type='password' name='form_rootpass' value='' />&nbsp;\n";
    echo "<input type='submit' name='form_delete' value='Yes, delete!' /></p>\n";
    echo "</center>\n</form>\n</body>\n</html>\n";
    exit();
  }

  if (!empty($_POST['form_delete']) && !empty($_POST['form_rootname'])) {
    $rootname = $_POST['form_rootname'];
    $rootpass = $_POST['form_rootpass'];
    // Do the delete and continue with the normal display.
    $sitedir = "$OE_SITES_BASE/$siteid";
    if (!is_dir($sitedir)               ) die("Not a directory: '$sitedir'");
    if (!is_file("$sitedir/sqlconf.php")) die("Not a file: '$sitedir/sqlconf.php'");
    include "$sitedir/sqlconf.php";
    if ($config) {
	    if ($host == "localhost")
		    $dbh = mysql_connect("$host","$rootname","$rootpass");
	    else
		    $dbh = mysql_connect("$host:$port","$rootname","$rootpass");
	    if (!$dbh) die("MySQL connection to host='$host' user='$rootname' password='$rootpass' failed!");
      $sql = "DROP DATABASE $dbase";
      if ($DEBUG) {
        echo "Would execute '$sql'.<br />\n";
      }
      else {
	      if (mysql_query($sql, $dbh) == FALSE) die("Failed: '$sql'");
      }
    }
    recurse_delete($sitedir);
  }
}
?>

<p><span class='title'>OpenEMR Site Administration</span></p>
<table width='100%' cellpadding='1' cellspacing='2'>
 <tr class='head'>
  <td>Site ID</td>
  <td>DB Name</td>
  <td>Site Name</td>
  <td>Version</td>
  <td>Select</td>
 </tr>
<?php
$dh = opendir($OE_SITES_BASE);
if (!$dh) die("Cannot read directory '$OE_SITES_BASE'.");
$siteslist = array();

while (false !== ($sfname = readdir($dh))) {
  if (substr($sfname, 0, 1) == '.') continue;
  if ($sfname == 'CVS'            ) continue;
  $sitedir = "$OE_SITES_BASE/$sfname";
  if (!is_dir($sitedir)               ) continue;
  if (!is_file("$sitedir/sqlconf.php")) continue;
  $siteslist[$sfname] = $sfname;
}

closedir($dh);
ksort($siteslist);

foreach ($siteslist as $sfname) {
  $sitedir = "$OE_SITES_BASE/$sfname";
  $errmsg = '';
  ++$encount;
  $bgcolor = "#" . (($encount & 1) ? "ddddff" : "ffdddd");

  echo " <tr class='detail' bgcolor='$bgcolor'>\n";

  // Access the site's database.
  include "$sitedir/sqlconf.php";

  if ($config) {
    $dbh = mysql_connect("$host:$port", "$login", "$pass");
    if ($dbh === FALSE)
      $errmsg = "MySQL connect failed";
    else if (!mysql_select_db($dbase, $dbh))
      $errmsg = "Access to database failed";
  }

  echo "  <td>$sfname</td>\n";
  echo "  <td>$dbase</td>\n";

  if (!$config) {
    echo "  <td colspan='3'><a href='setup.php?site=$sfname'>Needs setup, click here to run it</a></td>\n";
  }
  else if ($errmsg) {
    echo "  <td colspan='3' style='color:red'>$errmsg</td>\n";
  }
  else {
    // Get site name for display.
    $row = sqlQuery("SELECT gl_value FROM globals WHERE gl_name = 'openemr_name' LIMIT 1");
    $openemr_name = $row ? $row['gl_value'] : '';

    // Get version indicators from the database.
    $row = sqlQuery("SHOW TABLES LIKE 'version'");
    if (empty($row)) {
      $openemr_version = 'Unknown';
      $database_version = 0;
    }
    else {
      $row = sqlQuery("SELECT * FROM version LIMIT 1");
      $openemr_version = $row['v_major'] . "." . $row['v_minor'] . "." .
        $row['v_patch'] . $row['v_tag'];
      $database_version = 0 + $row['v_database'];
    }

    // Display relevant columns.
    echo "  <td>$openemr_name</td>\n";
    echo "  <td>$openemr_version";
    if ($v_database != $database_version) {
      echo " Needs upgrade!";
    }
    echo "</td>\n";

    echo "<td align='center'><input type='radio' name='form_site' value='$sfname'></td>\n";

    /*****************************************************************
    if ($v_database == $database_version) {
      echo "  <td><a href='interface/login/login_frame.php?site=$sfname'>Log In</a></td>\n";
    }
    else {
      echo "  <td><a href='sql_upgrade.php?site=$sfname'>Upgrade Database</a></td>\n";
    }
    *****************************************************************/
  }
  echo " </tr>\n";

  if ($config && $dbh !== FALSE) mysql_close($dbh);
}
?>
</table>
<p>
<input type='button' onclick='add_clicked()' value='Add New Site' />
&nbsp;&nbsp;Or with selected:
<select name='form_action'>
<option value='login'>Log In</option>
<option value='upgrade'>Upgrade Database</option>
<option value='delete'>Delete</option>
</select>
<input type='button' onclick='go_clicked()' value='Go' />
</p>
</center>
</form>
</body>
</html>

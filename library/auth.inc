<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

//----------THINGS WE ALWAYS DO

require_once("{$GLOBALS['srcdir']}/log.inc");
require_once("{$GLOBALS['srcdir']}/sql.inc");
// added for the phpGACL group check -- JRM
require_once("{$GLOBALS['srcdir']}/acl.inc");

$incoming_site_id = '';

if (isset($_GET['auth']) && ($_GET['auth'] == "login") && isset($_POST['authUser']) &&
    isset($_POST['authPass']) && isset($_POST['authProvider']))
{
    // set the language
    if (!empty($_POST['languageChoice'])) {
        $_SESSION['language_choice'] = $_POST['languageChoice'];
    }
    else {
        $_SESSION['language_choice'] = 1;
    }

    $ip=$_SERVER['REMOTE_ADDR'];
    if (!authNewSession($_POST['authUser'], $_POST['authPass'], $_POST['authProvider']))
    {
        newEvent("login",$_POST['authUser'], $_POST['authProvider'], "failure: $ip");
        $_SESSION['loginfailure'] = 1;
        authLoginScreen();
    }
    newEvent("login", $_POST['authUser'], $_POST['authProvider'], "success: $ip");
    $_SESSION['loginfailure'] = null;
    unset($_SESSION['loginfailure']);
    //store the very first initial timestamp for timeout errors
    $_SESSION["last_update"] = time();
}
else if ( (isset($_GET['auth'])) && ($_GET['auth'] == "logout") )
{
    newEvent("logout", $_SESSION['authUser'], $_SESSION['authProvider'], "success");
    authCloseSession();
    authLoginScreen();
}
else
{
    if (authCheckSession())
    {
        if (isset($_SESSION['pid']) && empty($GLOBALS['DAEMON_FLAG']))
        {
            require_once("{$GLOBALS['srcdir']}/patient.inc");
            $logpatient = getPatientData($_SESSION['pid'], "lname, fname, mname");
            newEvent("view", $_SESSION['authUser'], $_SESSION['authProvider'],
                "{$logpatient['lname']}, {$logpatient['fname']} {$logpatient['mname']} :: encounter " .
                $_SESSION['encounter']);
        }
        //LOG EVERYTHING
        //newEvent("view", $_SESSION['authUser'], $_SESSION['authProvider'], $_SERVER['REQUEST_URI']);
    }
    else {
        newEvent("login",$_POST['authUser'], $_POST['authProvider'], "insufficient data sent");
        authLoginScreen();
    }
}

if (!isset($_SESSION["last_update"])) {
    authLoginScreen();
} else {
     //if page has not been updated in a given period of time, we call login screen
    if ((time() - $_SESSION["last_update"]) > $timeout) {
        newEvent("logout", $_SESSION['authUser'], $_SESSION['authProvider'], "timeout");
        authCloseSession();
        authLoginScreen();
    } else {
        if (empty($GLOBALS['DAEMON_FLAG'])) $_SESSION["last_update"] = time();
    }
}

//----------THINGS WE DO IF WE STILL LIKE YOU

function authNewSession ($user, $pass, $provider)
{
    // check to see if the user belongs to *any* OpenEMR groups in phpGACL -- JRM
    global $phpgacl_location;
    if (isset ($phpgacl_location)) {
        if (acl_get_group_titles($user) == 0) return false;
    }

    // get details about the user
    $authDB = sqlQuery("select id, password, authorized, see_auth".
                        ", cal_ui, active ".
                        " from users where username = '$user'");

    // if the user is NOT active, get out
    if ($authDB['active'] != 1) { return false; }

    // globals.php already does this:
    /*****************************************************************
    // start the HTTP SESSION
    if(!session_id()) { session_start(); }
    *****************************************************************/

    // compare the submitted password with the stored password
    if ($authDB['password'] == $pass)
    {
        //here, we check to see if the user is in fact a member of the correct group:
        if ($authGroup = sqlQuery("select * from groups where user='$user' and name='$provider'"))
        {
            $_SESSION['authUser'] = $user;
            $_SESSION['authGroup'] = $authGroup['name'];
            $_SESSION['authUserID'] = $authDB['id'];
            $_SESSION['authPass'] = $pass;
            $_SESSION['authProvider'] = $provider;
            $_SESSION['authId'] = $authDB{'id'};
            $_SESSION['cal_ui'] = $authDB['cal_ui'];
            $_SESSION['userauthorized'] = $authDB['authorized'];
            // Some users may be able to authorize without being providers:
            if ($authDB['see_auth'] > '2') $_SESSION['userauthorized'] = '1';
            return true;
        } else {
            return false;
        }
    }
    else
        return false;
}

function authCheckSession ()
{
    if (isset($_SESSION['authId'])) {
        $authDB = sqlQuery("select username, password from users where id = '" .
            $_SESSION['authId']."'");
        if ($_SESSION['authUser'] == $authDB['username'] &&
            $_SESSION['authPass'] == $authDB['password'])
        {
            return true;
        }
        else {
            return false;
        }
    }
    else {
        return false;
    }
}

function authCloseSession ()
{
  // Before destroying the session, save its site_id so that the next
  // login will default to that same site.
  global $incoming_site_id;
  $incoming_site_id = $_SESSION['site_id'];
  ob_start();
  session_unset();
  session_destroy();
  unset($_COOKIE[session_name()]);
}

function authLoginScreen()
{
  // See comment in authCloseSession().
  global $incoming_site_id;
  header("Location: {$GLOBALS['login_screen']}?error=1&site=$incoming_site_id");
  exit;
}

function addUser ($username, $password_md5, $info, $authorized = 'yes')
{
    return sqlInsert("insert into users (username, password, info, authorized) values ('$username', '$password_md5', '$info', '$authorized')");
}

function delUser ($id)
{
    return sqlQuery("delete from users where id = '$id' limit 0,1");
}

function changePasword ($id, $new_md5)
{
    return sqlQuery("update users set password = '$new_md5' where id = '$id'");
}

function getUserList ($cols = '*', $limit = 'all', $start = '0')
{
    if ($limit = "all")
        $rez = sqlStatement("select $cols from users where username != '' order by date DESC");
    else
        $rez = sqlStatement("select $cols from users where username != '' order by date DESC limit $limit, $start");
    for ($iter = 0; $row = sqlFetchArray($rez); $iter++)
        $tbl[$iter] = $row;
    return $tbl;
}

function getProviderList ($cols = '*', $limit= 'all', $start = '0')
{
    if ($limit = "all")
        $rez = sqlStatement("select $cols from groups order by date DESC");
    else
        $rez = sqlStatement("select $cols from groups order by date DESC limit $limit, $start");
    for ($iter = 0; $row = sqlFetchArray($rez); $iter++)
        $tbl[$iter] = $row;
    return $tbl;
}

function addGroup ($groupname)
{
    return sqlInsert("insert into groups (name) values ('$groupname')");
}

function delGroup ($group_id)
{
    return sqlQuery("delete from groups where id = '$group_id' limit 0,1");
}

/***************************************************************
//pennfirm
//Function currently user by new post calendar code to determine
//if a given user is in a group with another user
//and if so to allow editing of that users events
//
//*************************************************************/

function validateGroupStatus ($user_to_be_checked, $group_user) {
    if (isset($user_to_be_checked) && isset($group_user)) {
        if ($user_to_be_checked == $group_user) {

            return true;
        }
        elseif ($_SESSION['authorizeduser'] == 1)
            return true;

        $query = "SELECT groups.name FROM users,groups WHERE users.username =  \"" . mysql_real_escape_string($user_to_be_checked) . "\" " .
                 "AND users.username = groups.user group by groups.name";
        $result = sqlStatement($query);

        $usertbcGroups = array();

        while ($row = mysql_fetch_array($result)) {
            $usertbcGroups[] = $row[0];
        }

        $query = "SELECT groups.name FROM users,groups WHERE users.username =  \"" . mysql_real_escape_string($group_user) . "\" " .
                 "AND users.username = groups.user group by groups.name";
        $result = sqlStatement($query);

        $usergGroups = array();

        while ($row = mysql_fetch_array($result)) {
            $usergGroups[] = $row[0];
        }
        foreach ($usertbcGroups as $group) {
              if(in_array($group,$usergGroups)) {
              return true;
            }
        }

    }

    return false;
}
?>

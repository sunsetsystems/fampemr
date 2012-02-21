<?php
require_once("{$GLOBALS['srcdir']}/sql.inc");

function newEvent($event, $user, $groupname, $comments="") {
    $adodb = $GLOBALS['adodb']['db'];
    $sql = "insert into log ( date, event, user, groupname, comments ) " .
            "values ( NOW(), " . $adodb->qstr($event) . "," . $adodb->qstr($user) .
            "," . $adodb->qstr($groupname) . "," .$adodb->qstr($comments) . ")";
    return sqlInsertClean($sql);
}

function getEventByDate($date, $user="", $cols="DISTINCT date, event, user, groupname, comments")
{
    $sql = "SELECT $cols FROM log WHERE date >= '$date 00:00:00' AND date <= '$date 23:59:59'";
    if ($user) $sql .= " AND user LIKE '$user'";
    $sql .= " ORDER BY date DESC LIMIT 5000";
    $res = sqlStatement($sql);
    for($iter=0; $row=sqlFetchArray($res); $iter++) {
        $all[$iter] = $row;
    }
    return $all;
}

/******************
 * Get records from the LOG table
 * using the optional parameters:
 *   sdate : start date  (defaults to today)
 *   edate : end date  (defaults to today)
 *   user  : a specific user  (defaults to none)
 *   levent: event name
 *   cols  : gather specific columns  (defaults to date,event,user,groupname,comments)
 *   sortby: sort the results by  (defaults to none)
 * RETURNS:
 *   array of results
 ******************/
function getEvents($params) 
{
    // parse the parameters
    $cols = "DISTINCT date, event, user, groupname, comments";
    if (isset($params['cols']) && $params['cols'] != "") $cols = $params['cols'];

    $date1 = date("Y-m-d", time());
    if (isset($params['sdate']) && $params['sdate'] != "") $date1= $params['sdate'];
    
    $date2 = date("Y-m-d", time());
    if (isset($params['edate']) && $params['edate'] != "") $date2= $params['edate'];
    
    $user = "";
    if (isset($params['user']) && $params['user'] != "") $user= $params['user'];
    
    $sortby = "";
    if (isset($params['sortby']) && $params['sortby'] != "") $sortby = $params['sortby'];
    
    $levent = "";
    if (isset($params['levent']) && $params['levent'] != "") $levent = $params['levent'];

    // do the query
    $sql = "SELECT $cols FROM log WHERE date >= '$date1 00:00:00' AND date <= '$date2 23:59:59'";
    if ($user != "") $sql .= " AND user LIKE '$user'";
    if ($levent != "") $sql .= " AND event LIKE '$levent%'";
    if ($sortby != "") $sql .= " ORDER BY " . $sortby . " DESC "; // descending order
    $sql .= " LIMIT 5000";

    $res = sqlStatement($sql);
    for ($iter = 0; $row = sqlFetchArray($res); $iter++) {
        $all[$iter] = $row;
    }
    return $all;
}
?>

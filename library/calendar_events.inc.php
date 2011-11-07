<?php
// Copyright (C) 2010-2011 Rod Roark <rod@sunsetsystems.com>
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// Get a result set of squad events for the given squad, player and day.
// This is only useful for sports teams.
//
function getSquadEvents($date, $squad, $plid) {
  return sqlStatement("SELECT e.pc_eid, e.pc_hometext, " .
    "e.pc_eventDate, e.pc_endDate, e.pc_startTime, " .
    "e.pc_duration, e.pc_recurrtype, e.pc_recurrspec, " .
    "p.pid, p.minutes, p.fitness_related " .
    "FROM openemr_postcalendar_events AS e " .
    "JOIN openemr_postcalendar_categories AS c ON " .
    "c.pc_catdesc LIKE 'Squad=$squad' AND c.pc_catid = e.pc_catid " .
    "LEFT JOIN player_event AS p ON " .
    "p.pid = '$plid' AND p.date = '$date' AND p.pc_eid = e.pc_eid " .
    "WHERE ((e.pc_endDate >= '$date' AND e.pc_eventDate <= '$date') OR " .
    "(e.pc_endDate = '0000-00-00' AND e.pc_eventDate = '$date')) " .
    "ORDER BY e.pc_startTime, e.pc_eid");
}

// Determine if the specified event applies to the specified date (YYYY-MM-DD).
//
function eventMatchesDay($row, $date) {
  $time1 = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
  $time2 = $time1 + (24 * 60 * 60);

  $thistime = strtotime($row['pc_eventDate'] . " 00:00:00");
  if ($row['pc_recurrtype']) {

    preg_match('/"event_repeat_freq_type";s:1:"(\d)"/', $row['pc_recurrspec'], $matches);
    $repeattype = $matches[1];

    preg_match('/"event_repeat_freq";s:1:"(\d)"/', $row['pc_recurrspec'], $matches);
    $repeatfreq = $matches[1];
    if (! $repeatfreq) $repeatfreq = 1;

    $endtime = strtotime($row['pc_endDate'] . " 00:00:00") + (24 * 60 * 60);
    if ($endtime > $time2) $endtime = $time2;

    // Shortcut for events that repeat every day.
    if ($repeattype == 0 && $repeatfreq == 1)
      return ($thistime < $time2 && $endtime >= $time1);

    $repeatix = 0;
    while ($thistime < $endtime) {
      if ($repeatix == 0 && $thistime >= $time1) return true;
      if (++$repeatix >= $repeatfreq) $repeatix = 0;
      $adate = getdate($thistime);
      if ($repeattype == 0)        { // daily
        $adate['mday'] += 1;
      } else if ($repeattype == 1) { // weekly
        $adate['mday'] += 7;
      } else if ($repeattype == 2) { // monthly
        $adate['mon'] += 1;
      } else if ($repeattype == 3) { // yearly
        $adate['year'] += 1;
      } else if ($repeattype == 4) { // work days
        if ($adate['wday'] == 5)      // if friday, skip to monday
          $adate['mday'] += 3;
        else if ($adate['wday'] == 6) // saturday should not happen
          $adate['mday'] += 2;
        else
          $adate['mday'] += 1;
      } else {
        die("Invalid repeat type '$repeattype'");
      }
      $thistime = mktime(0, 0, 0, $adate['mon'], $adate['mday'], $adate['year']);
    }
  } else { // not recurring
    return ($thistime >= $time1 && $thistime < $time2);
  }

  return false; // repeating event did not match
}

// This is called to update the appointment status for a specified patient
// with an encounter on the specified date.  It does nothing unless the
// feature to auto-update appointment statuses is enabled.
function updateAppointmentStatus($pid, $encdate, $newstatus) {
  if (empty($GLOBALS['gbl_auto_update_appt_status'])) return;
  // Find appointment and set appointment status as appropriate.
  // This makes some assumptions about what the status IDs are.
  $query = "SELECT pc_eid, pc_apptstatus " .
    "FROM openemr_postcalendar_events WHERE " .
    "pc_pid = '$pid' AND pc_recurrtype = 0 AND " .
    "pc_eventDate = '$encdate' " .
    "ORDER BY pc_startTime DESC, pc_eid DESC LIMIT 1";
  $tmp = sqlQuery($query);
  if (!empty($tmp['pc_eid'])) {
    $appt_eid = $tmp['pc_eid'];
    $appt_status = $tmp['pc_apptstatus'];
    // Some tests for illogical changes.
    if ($appt_status == '$') return;
    if ($newstatus == '<' && $appt_status == '>') return;
    sqlStatement("UPDATE openemr_postcalendar_events SET " .
      "pc_apptstatus = '$newstatus' WHERE pc_eid = '$appt_eid'");
  }
}
?>

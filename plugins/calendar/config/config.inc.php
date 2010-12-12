<?php
/**
 * RoundCube Calendar
 *
 * Plugin to add a calendar to RoundCube.
 *
 * @version 0.2 BETA 2
 * @author Lazlo Westerhof
 * @url http://rc-calendar.lazlo.me
 * @licence GNU GPL
 * @copyright (c) 2010 Lazlo Westerhof - Netherlands
 *
 **/

// backend type (dummy, database, google, caldav), see README
// Note: "dummy" is only for demonstrating basic functionality.
$cmail_config['backend'] = "database";

// default calendar view (agendaDay, agendaWeek, month)
$cmail_config['default_view'] = "agendaWeek";

// time format (HH:mm, H:mm, h:mmt)
$cmail_config['time_format'] = "HH:mm";

// timeslots per hour (1, 2, 3, 4, 6)
$cmail_config['timeslots'] = 2;

// first day of the week (eg Sunday, Monday)
$cmail_config['first_day'] = "sunday";

// first hour of the calendar (0-23)
$cmail_config['first_hour'] = 6;

// event categories
$cmail_config['categories'] = array('Personal' => 'c0c0c0', 
                                         'Work' => 'ff0000',
                                       'Family' => '00ff00',
                                      'Holiday' => 'ff6600');

// Settings for CalDAV backend.
#Example For Google Calendar
#$cmail_config['caldav_server'] = 'https://google.com/calendar/dav/username@gmail.com/user/';
#$cmail_config['caldav_username'] = 'username';
#$cmail_config['caldav_password'] = 'password';
#$cmail_config['caldav_calendar'] = 'Calandar Name';

// --- Options for using RoundCube account for CalDAV authentication
//     (useful for LDAP)
//
// 1. If true, use login and password from RoundCube
$cmail_config['caldav_use_crystalmail_login'] = false;
// 2. If true, strip top level domain (tld) from username
//    (username@domain.com -> username) for CalDAV login
//    Has no effect if no domain is used for RoundCube login.
$cmail_config['caldav_loginwithout_tld'] = true;
?>
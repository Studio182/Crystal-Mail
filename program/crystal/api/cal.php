<?php
/*
+-------------------------------------------------------------------+
| ./program/crystal/api/cal.php                                     |
|                                                                   |
| This file is part of the Crystal Mail Client                      |
| Copyright (C) 2010-2011, Crystal Mail Dev. Team - United States   |
|                                                                   |
| Licensed under the GNU GPL                                        |
|                                                                   |
| PURPOSE:                                                          |
|   The Calendar API Script                                         |
|                                                                   |
+----------------------- Studio 182 Team ---------------------------+
| Hunter Dolan <hunter@crystalmail.net>                             |
| Pablo Merino <pablo@studio182.net>                                |
+-------------------------------------------------------------------+
*/

if($allowapi == true) {
	$cmail = cmail::get_instance();
		//Stay UP!

 function init() {
    $cmail = cmail::get_instance();
    
    if(file_exists("../../../plugins/calendar/config/config.inc.php")) {
      $cmail->load_config('../../../plugins/calendar/config/config.inc.php');
    } else {
      $cmail->load_config('../../../plugins/calendar/config/config.inc.php.dist'); 
    }
    
    $backend_type = $cmail->config->get('backend', 'dummy');
    require('../../../plugins/calendar/program/backend/' . $backend_type . '.php');

    if($backend_type === "google") {
      $backend = new Google($cmail,
                                  $cmail->config->get('username'), 
                                  $cmail->config->get('password'));
    } else if($backend_type === "caldav") {
      $myusername = $cmail->config->get('caldav_username');
      $mypassword = $cmail->config->get('caldav_password');
      
      if ($cmail->config->get('caldav_use_crystalmail_login') === true) {
        $myusername = $_SESSION['username'];
        $mypassword = $cmail->decrypt($_SESSION['password']);
        
        // Strip top-level domain from login (username@domain.com -> username)
        if ($cmail->config->get('username_domain') !== '' /* global RoundCube setting */
          && $cmail->config->get('caldav_loginwithout_tld') === true) {
          $a_myusername = explode('@', $_SESSION['username'], 2);
          
          if ($a_myusername !== false && !empty($a_myusername))
            $myusername = $a_myusername[0];
        }
      }
      
      $backend = new CalDAV($cmail,
                                  $cmail->config->get('caldav_server'),
                                  $myusername,
                                  $mypassword,
                                  $cmail->config->get('caldav_calendar') /* FIXME currenty ignored */);
    } else if($backend_type === "database") {
      $backend = new Database($cmail);
    } else {
      $backend = new Dummy();
    }

    // Set up utils
    require('../../../plugins/calendar/program/utils.php');
    $utils = new Utils($cmail, $backend);
}
	
	init();
	
	echo $backend->calelndar->jsonEvents(date('Y-m-d H:i:s'), strtotime(date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s"))) . " +1 week"));
	
		//Stay DOWN
}
?>
<?php
  //##########################################################
  //  Crystal Mail Admin Panel IMAP Authentication Script    #
  //                   By: Hunter Dolan                      #
  //                Written on June 7, 2010                  #
  //  Last Ammendment: September 4th 2010 (By: Hunter Dolan) #
  //               Licensed under GNU GPL 3.0!               #          
  //##########################################################
  
  //Start Session
  ini_set("display_errors", 0);
  session_start();
  
  include('../config/main.inc.php');
  
  if (file_exists('../skins/' . $cmail_config['skin'] . '/admin/login.php')) {
      $login = '../skins/' . $cmail_config['skin'] . '/admin/login.php';
  } else {
      $login = '../skins/crystal/admin/login.php';
  }
 //Check if we are logging out
  if ($_GET['_action'] == "logout") {
      session_destroy();
      echo '<meta http-equiv="refresh" content="0;url=index.php">';
      die();
  }
  
  //Check if there is already an active session for this user
  if (empty($_SESSION['user'])) {
      //Check if we are trying to login
      if (empty($_POST['user'])) {
          //If not show the login screen
          include($login);
          die();
      } else {
 
if (in_array($_POST['user'], $cmail_config['users'])) {
$pass = array_search($_POST['user'], $cmail_config['users']);
$_POST['pass'] = md5($_POST['pass']);
if ($_POST['pass'] == $pass) {
$_SESSION['user'] = $_POST['user'];
$_SESSION['pass'] = $_POST['pass'];
} else {
 include ($login);
 die();
 }
  } else {
  include ($login);
  die();
  }
} 
} else {
//Reauth (just to be safe)
 if (in_array($_SESSION['user'], $cmail_config['users'])) {
$pass = array_search($_SESSION['user'], $cmail_config['users']);
$_POST['pass'] = md5($_SESSION['pass']);
if ($_SESSION['pass'] == $pass) {
} else {
 include ($login);
 die();
 }
  } else {
  include ($login);
  die();
  }
} 
?>

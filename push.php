<?php

$allowapi = true;
require_once 'program/include/iniset.php';

 $cmail = cmail::get_instance();
  
$user = array('user@domain' => 'pass', 'user@domain2' => 'pass2');

foreach($user as $user=>$pass) {
   $auth['user'] = $user;
   $auth['pass'] = $pass;
   
if(!$cmail->login($auth['user'], $auth['pass'], '')) {
die('error');
//handle error!
}
$cmail = cmail::get_instance();
$IMAP = new crystal_IMAP(null);
if ($_SESSION['imap_ssl'] == 'ssl') {$imap_ssl = true;} else {$imap_ssl = false;}
if (!$IMAP->connect($_SESSION['imap_host'], $_SESSION['username'], $cmail->decrypt($_SESSION['password']), $_SESSION['imap_port'], $imap_ssl)) {
die(json_encode(array('error' => '104', 'human_error' => 'IMAP Connection Failed')));
}
$mailbox = "INBOX";
$unread = $IMAP->messagecount($mailbox, 'UNSEEN');
if($unread > 0) {
file_get_contents('http://localhost/apns/?uid=59d5ce2f1e15492d26b7a1046443c4d62cbbce9239c907af1cb7e3e0dea26dac&badge='.$unread);
}
}
?>
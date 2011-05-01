<?php
switch($_GET['_task']) {
case unread_count:
$cmail = cmail::get_instance();
$IMAP = new crystal_IMAP(null);
if ($_SESSION['imap_ssl'] == 'ssl') {$imap_ssl = true;} else {$imap_ssl = false;}
if (!$IMAP->connect($_SESSION['imap_host'], $_SESSION['username'], $cmail->decrypt($_SESSION['password']), $_SESSION['imap_port'], $imap_ssl)) {
die(json_encode(array('error' => '104', 'human_error' => 'IMAP Connection Failed')));
}
$mailbox = $_GET['mailbox'];
$unread = $IMAP->messagecount($mailbox, 'UNSEEN');
echo $unread;
break;
case messages:
$cmail = cmail::get_instance();
$IMAP = new crystal_IMAP(null);
if ($_SESSION['imap_ssl'] == 'ssl') {$imap_ssl = true;} else {$imap_ssl = false;}
if (!$IMAP->connect($_SESSION['imap_host'], $_SESSION['username'], $cmail->decrypt($_SESSION['password']), $_SESSION['imap_port'], $imap_ssl)) {
die(json_encode(array('error' => '104', 'human_error' => 'IMAP Connection Failed')));
}
$current = $IMAP->get_mailbox_name();
//$mailboxes = $IMAP->list_mailboxes();
$IMAP->set_mailbox('INBOX');
$messages = $IMAP->message_index('INBOX');
//print_r($messages);
//die();

$messages = array_slice($messages, 0, 50);

//$messages = array('1486','1485');
$message_array = array();

foreach ($messages as $message) {
$m = $IMAP->get_headers($message, 'INBOX', false, false);
$m = array('id' => $m->id, 'to' => $m->to, 'uid' => $m->uid, 'subject' => $m->subject, 'from' => $m->from, 'to' => $m->to, 'cc' => $m->cc, 'replyto' => $m->replyto, 'in_reply_to' => $m->in_reply_to, 'date' => $m->date, 'size' => $m->size, 'flags' => $m->flags, 'timestamp' => $m->timestamp);

array_push($message_array, $m);
}

echo json_encode(array('error' => 0, 'messages' => $message_array));
break;

	case folders:
	
$cmail = cmail::get_instance();
$IMAP = new crystal_IMAP(null);
if ($_SESSION['imap_ssl'] == 'ssl') {$imap_ssl = true;} else {$imap_ssl = false;}
if (!$IMAP->connect($_SESSION['imap_host'], $_SESSION['username'], $cmail->decrypt($_SESSION['password']), $_SESSION['imap_port'], $imap_ssl)) {
die(json_encode(array('error' => '104', 'human_error' => 'IMAP Connection Failed')));
}
$current = $IMAP->get_mailbox_name();
$mailbox_array = array();
$mailboxes = $IMAP->list_mailboxes();
foreach ($mailboxes as $mailbox) {
$unread = $IMAP->messagecount($mailbox, 'UNSEEN');
$all = $IMAP->messagecount($mailbox);
$mailbox = array($mailbox => array('unread' => $unread, 'all' => $all));
array_push($mailbox_array, $mailbox);
}


echo json_encode(array('error' => 0, 'folders' => $mailbox_array));
break;

}


?>
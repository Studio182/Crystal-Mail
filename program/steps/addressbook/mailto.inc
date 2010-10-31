<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/list.inc                           |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2010, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Send contacts list to client (as remote response)            |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


$cid = get_input_value('_cid', crystal_INPUT_GET);
$recipients = null;
$mailto = array();

if ($cid && preg_match('/^[a-z0-9\+\/=_-]+(,[a-z0-9\+\/=_-]+)*$/i', $cid) && $CONTACTS->ready)
{
  $CONTACTS->set_page(1);
  $CONTACTS->set_pagesize(100);
  $recipients = $CONTACTS->search($CONTACTS->primary_key, $cid);

  while (is_object($recipients) && ($rec = $recipients->iterate()))
    $mailto[] = format_email_recipient($rec['email'], $rec['name']);
}

if (!empty($mailto))
{
  $mailto_str = join(', ', $mailto);
  $mailto_id = substr(md5($mailto_str), 0, 16);
  $_SESSION['mailto'][$mailto_id] = urlencode($mailto_str);
  $OUTPUT->redirect(array('task' => 'mail', '_action' => 'compose', '_mailto' => $mailto_id));
}
else
  $OUTPUT->show_message('nocontactsfound', 'warning');


// send response
$OUTPUT->send();


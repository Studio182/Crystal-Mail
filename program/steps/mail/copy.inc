<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/copy.inc                                  |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2010, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

// only process ajax requests
if (!$OUTPUT->ajax_call)
  return;

// count messages before changing anything
$old_count = $IMAP->messagecount(NULL, $IMAP->threading ? 'THREADS' : 'ALL');
$old_pages = ceil($old_count / $IMAP->page_size);

// move messages
if (!empty($_POST['_uid']) && !empty($_POST['_target_mbox'])) {
    $uids = get_input_value('_uid', crystal_INPUT_POST);
    $target = get_input_value('_target_mbox', crystal_INPUT_POST);
    $mbox = get_input_value('_mbox', crystal_INPUT_POST);

    $copied = $IMAP->copy_message($uids, $target, $mbox);
  
    if (!$copied) {
        // send error message
        $OUTPUT->show_message('errorcopying', 'error');
        $OUTPUT->send();
        exit;
    }

    cmail_send_unread_count($target, true);

    $OUTPUT->command('set_quota', cmail_quota_content());
}
// unknown action or missing query param
else {
    exit;
}

// send response
$OUTPUT->send();



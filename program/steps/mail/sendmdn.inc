<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/sendmdn.inc                               |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2008-2009, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Send a message disposition notification for a specific mail  |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


// only process ajax requests
if (!$OUTPUT->ajax_call)
  return;

if (!empty($_POST['_uid'])) {
  $sent = cmail_send_mdn(get_input_value('_uid', crystal_INPUT_POST), $smtp_error);
}

// show either confirm or error message
if ($sent) {
  $OUTPUT->set_env('mdn_request', false);
  $OUTPUT->show_message('receiptsent', 'confirmation');
}
else if ($smtp_error) {
  $OUTPUT->show_message($smtp_error['label'], 'error', $smtp_error['vars']);
}
else {
  $OUTPUT->show_message('errorsendingreceipt', 'error');
}

$OUTPUT->send();

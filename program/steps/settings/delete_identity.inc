<?php
/*
+----------------------------------------------------------------+
| ./program/steps/settings/delete_identity.inc                   |
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
|   Delete the submitted identities (IIDs) from the database     |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

$iid = get_input_value('_iid', crystal_INPUT_GPC);

// check request token
if (!$OUTPUT->ajax_call && !$cmail->check_request(crystal_INPUT_GPC)) {
  $OUTPUT->show_message('invalidrequest', 'error');
  cmail_overwrite_action('identities');
  return;
}

if ($iid && preg_match('/^[0-9]+(,[0-9]+)*$/', $iid))
{
  $plugin = $cmail->plugins->exec_hook('delete_identity', array('id' => $iid));
  
  if (!$plugin['abort'] && $USER->delete_identity($iid)) {
    $OUTPUT->show_message('deletedsuccessfully', 'confirmation', null, false);
  }
  else {
    $OUTPUT->show_message('nodeletelastidentity', 'error', null, false);
  }
  // send response
  if ($OUTPUT->ajax_call)
    $OUTPUT->send();
}

if ($OUTPUT->ajax_call)
  exit;

// go to identities page
cmail_overwrite_action('identities');



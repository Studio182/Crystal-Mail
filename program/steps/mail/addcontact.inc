<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/addcontact.inc                            |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2009, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Add the submitted contact to the users address book          |
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

$done = false;
$CONTACTS = $cmail->get_address_book(null, true);

if (!empty($_POST['_address']) && is_object($CONTACTS))
{
  $contact_arr = $IMAP->decode_address_list(get_input_value('_address', crystal_INPUT_POST, true), 1, false);
  
  if (!empty($contact_arr[1]['mailto']))
  {
    $contact = array(
      'email' => $contact_arr[1]['mailto'],
      'name' => $contact_arr[1]['name']
    );
    
    // use email address part for name
    if (empty($contact['name']) || $contact['name'] == $contact['email'])
      $contact['name'] = ucfirst(preg_replace('/[\.\-]/', ' ', substr($contact['email'], 0, strpos($contact['email'], '@'))));

    // check for existing contacts
    $existing = $CONTACTS->search('email', $contact['email'], true, false);
    if ($done = $existing->count)
      $OUTPUT->show_message('contactexists', 'warning');
    else
    {
      $plugin = $cmail->plugins->exec_hook('contact_create', array('record' => $contact, 'source' => null));
      $contact = $plugin['record'];

      if (!$plugin['abort'] && ($done = $CONTACTS->insert($contact)))
        $OUTPUT->show_message('addedsuccessfully', 'confirmation');
    }
  }
}

if (!$done)
  $OUTPUT->show_message('errorsavingcontact', 'warning');

$OUTPUT->send();


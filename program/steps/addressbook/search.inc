<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/search.inc                         |
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
|   Search step for address book contacts                        |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


$CONTACTS->set_page(1);
$_SESSION['page'] = 1;

$search = trim(get_input_value('_q', crystal_INPUT_GET));
$search_request = md5('addr'.$search);

// get contacts for this user
$result = $CONTACTS->search(array('name','email'), $search);

if ($result->count > 0)
{
  // save search settings in session
  $_SESSION['search'][$search_request] = $CONTACTS->get_search_set();

  // create javascript list
  cmail_js_contacts_list($result);
}
else
{
  $OUTPUT->show_message('nocontactsfound', 'warning');
  $search_request = null;
}

// update message count display
$OUTPUT->set_env('search_request', $search_request);
$OUTPUT->set_env('pagecount', ceil($result->count / $CONTACTS->page_size));
$OUTPUT->command('set_rowcount', cmail_get_rowcount_text());
  
// send response
$OUTPUT->send();

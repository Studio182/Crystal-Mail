<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/search.inc                                  |
 |                                                                       |
 | This file is part of the Crystal Mail client                     |
 | Copyright (C) 2005-2007, crystalmail Dev. - Switzerland                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Search step for address book contacts                               |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <crystalmail@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: search.inc 456 2007-01-10 12:34:33Z thomasb $

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

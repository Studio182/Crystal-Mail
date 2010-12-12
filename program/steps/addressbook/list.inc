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


// get contacts for this user
$result = $CONTACTS->list_records();

// update message count display
$OUTPUT->set_env('pagecount', ceil($result->count / $CONTACTS->page_size));
$OUTPUT->command('set_rowcount', cmail_get_rowcount_text($rowcount));

// create javascript list
cmail_js_contacts_list($result);
  
// send response
$OUTPUT->send();


<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/export.inc                         |
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
|   Export the selected address book as vCard file               |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


// get contacts for this user
$CONTACTS->set_page(1);
$CONTACTS->set_pagesize(99999);
$result = $CONTACTS->list_records(null, 0, true);

// send downlaod headers
send_nocacheing_headers();
header('Content-Type: text/x-vcard; charset='.cmail_CHARSET);
header('Content-Disposition: attachment; filename="crystal_contacts.vcf"');

while ($result && ($row = $result->next())) {
  $vcard = new crystal_vcard($row['vcard']);
  $vcard->set('displayname', $row['name']);
  $vcard->set('firstname', $row['firstname']);
  $vcard->set('surname', $row['surname']);
  $vcard->set('email', $row['email']);
  
  echo $vcard->export();
}

exit;


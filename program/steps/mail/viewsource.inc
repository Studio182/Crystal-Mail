<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/viewsource.inc                            |
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
|   Display a mail message similar as a usual mail application   |
|   does                                                         |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


ob_end_clean();

// similar code as in program/steps/mail/get.inc
if ($uid = get_input_value('_uid', crystal_INPUT_GET))
{
  $headers = $IMAP->get_headers($uid);
  $charset = $headers->charset ? $headers->charset : $CONFIG['default_charset'];
  header("Content-Type: text/plain; charset={$charset}");

  if (!empty($_GET['_save'])) {
    $filename = ($headers->subject ? $IMAP->decode_header($headers->subject) : 'crystalmail') . '.eml';
    $browser = new crystal_browser;
    
    if ($browser->ie && $browser->ver < 7)
      $filename = rawurlencode(abbreviate_string($filename, 55));
    else if ($browser->ie)
      $filename = rawurlencode($filename);
    else
      $filename = addcslashes($filename, '"');

    header("Content-Length: {$headers->size}");
    header("Content-Disposition: attachment; filename=\"$filename\"");
  }
  
  $IMAP->print_raw_body($uid);
}
else
{
  raise_error(array(
      'code' => 500,
      'type' => 'php',
      'file' => __FILE__, 'line' => __LINE__,
      'message' => 'Message UID '.$uid.' not found'),
    true, true);
}

exit;


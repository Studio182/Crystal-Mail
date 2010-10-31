<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/headers.inc                               |
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
|   Fetch message headers in raw format for display              |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Aleksander 'A.L.E.C' Machniak <alec@alec.pl>                   |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

if ($uid = get_input_value('_uid', crystal_INPUT_POST))
{
    $source = $IMAP->get_raw_headers($uid);

    if ($source !== false) {
        $source = htmlspecialchars(trim($source));
        $source = preg_replace(
            array(
                '/\n[\t\s]+/',
                '/^([a-z0-9_:-]+)/im',
                '/\r?\n/'
            ),
            array(
                "\n&nbsp;&nbsp;&nbsp;&nbsp;",
                '<font class="bold">\1</font>',
                '<br />'
            ), $source);

        $OUTPUT->command('set_headers', $source);
    }
    else {
        $cmail->output->show_message('messageopenerror', 'error');
    }

    $OUTPUT->send();
}

exit;



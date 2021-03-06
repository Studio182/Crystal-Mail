<?php
/*
+----------------------------------------------------------------+
| ./program/steps/mail/check_recent.inc                          |
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
|   Check for recent messages, in all mailboxes                  |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/


$current = $IMAP->get_mailbox_name();
$check_all = !empty($_GET['_refresh']) || (bool)$cmail->config->get('check_all_folders');
$a_mailboxes = $check_all ? $IMAP->list_mailboxes() : (array)$current;

// check recent/unseen counts for all mailboxes
foreach ($a_mailboxes as $mbox_name) {
    if ($mbox_name == $current && ($status = $IMAP->mailbox_status($mbox_name))) {

        cmail_send_unread_count($mbox_name, true);

        // refresh saved search set
        $search_request = get_input_value('_search', crystal_INPUT_GPC);
        if ($search_request && isset($_SESSION['search'][$search_request])) {
            $_SESSION['search'][$search_request] = $IMAP->refresh_search();
        }

        if (!empty($_GET['_quota']))
            $OUTPUT->command('set_quota', cmail_quota_content());

        // "No-list" mode, don't get messages
        if (empty($_GET['_list']))
            continue;

        // get overall message count; allow caching because crystal_imap::mailbox_status() did a refresh
        $all_count = $IMAP->messagecount(null, $IMAP->threading ? 'THREADS' : 'ALL');

        // check current page if we're not on the first page
        if ($all_count && $IMAP->list_page > 1) {
            $remaining = $all_count - $IMAP->page_size * ($IMAP->list_page - 1);
            if ($remaining <= 0) {
                $IMAP->set_page($IMAP->list_page-1);
                $_SESSION['page'] = $IMAP->list_page;
            }
        }

        $OUTPUT->set_env('messagecount', $all_count);
        $OUTPUT->set_env('pagecount', ceil($all_count/$IMAP->page_size));
        $OUTPUT->command('set_rowcount', cmail_get_messagecount_text($all_count));
        $OUTPUT->set_env('current_page', $all_count ? $IMAP->list_page : 1);

        if ($status & 1) {
            if ($cmail->config->get('focus_on_new_message', true))
                $OUTPUT->command('new_message_focus');
            // trigger plugin hook
            $cmail->plugins->exec_hook('new_messages', array('mailbox' => $mbox_name));
        }

        // remove old rows (and clear selection if new list is empty)
        $OUTPUT->command('message_list.clear', $all_count ? false : true);

        if ($all_count) {
            $a_headers = $IMAP->list_headers($mbox_name, null, $_SESSION['sort_col'], $_SESSION['sort_order']);
            // add message rows
            cmail_js_message_list($a_headers, false);
            // remove messages that don't exists from list selection array
            $OUTPUT->command('update_selection');
        }
    }
    else {
        cmail_send_unread_count($mbox_name, true);
    }
}

$OUTPUT->send();



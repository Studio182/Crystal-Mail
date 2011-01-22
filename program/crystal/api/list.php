<?php
$IMAP = new crystal_IMAP();
$IMAP->set_mailbox(($_SESSION['mbox'] = 'INBOX'));

?>
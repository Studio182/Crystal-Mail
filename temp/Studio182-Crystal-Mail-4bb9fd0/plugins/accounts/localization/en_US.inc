<?php

$labels = array();
$labels['remoteaccounts'] = 'Remote IMAP Accounts';
$labels['account_dn'] = 'Account Name';
$labels['account_id'] = 'Login ID';
$labels['account_pw'] = 'Password';
$labels['account_pw_conf'] = 'Confirm Password';
$labels['account_host'] = 'IMAP Host';
$labels['add'] = 'Add';
$labels['back'] = 'Abort';
$labels['submit'] = 'Submit';
$labels['archivefolder'] = 'Archive';
$labels['remotefolders'] = 'Special Folders';

$messages = array();
$messages['dnempty'] = 'Please enter a name for this account';
$messages['userempty'] = 'Please enter account login ID';
$messages['passwordempty'] = 'Please enter password';
$messages['passwordnotmatch'] = 'Password does not match';
$messages['hostempty'] = 'Please enter host url (f.e. ssl://imap.gmail.com:993)';
$messages['accountexists'] = 'This account already exists. You have been redirected to edit mode.';
$messages['connectionfailed'] = 'Failed to establish connection to remote host.';

?>
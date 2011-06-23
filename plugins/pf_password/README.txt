 -----------------------------------------------------------------------
    Postfix Password Change Plugin for Crystal Mail
 -----------------------------------------------------------------------

 Some changes made to add support to:
  -PostFixAdmin 
  -CRAM-MD5 (dovecot's encryption)
  -MobileCube theme(the tab does not display in this theme)

-----------------------------------------------------------------------

 Created by Marcelo Salgado <msscelo@gmail.com>

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License version 2
 as published by the Free Software Foundation.

 -----------------------------------------------------------------------

 To develop this plugin, I used the password plugin as base.
 The password plugins was created by Aleksander Machniak <alec@alec.pl>

 -----------------------------------------------------------------------

 To install:
    1 - copy the pf_password directory to roundcube/plugins/

    2 - edit roundcube/plugins/pf_password/config.inc.php
   
    3 - edit roundcube/config/main.inc.php
    edit/add the following config:

    $cmail_config['plugins']
    add 'pf_password' to the array. if this line does not exist, add this:
    $cmail_config['plugins'] = array('pf_password');

    4 - make sure pf_password/drivers/dovecotpw has execute permission.


    If you want the plugin to not allow users to use the same password he used
    last time, do the following steps:
    1- edit your postfixadmin database, alter the mailbox table, add two 
    columns, 'last_password' and 'second_last_password' with the same properties
    as the 'password' column


    ALTER TABLE mailbox ADD last_password VARCHAR(255) NOT NULL AFTER password;
    ALTER TABLE mailbox ADD second_last_password VARCHAR(255) NOT NULL AFTER last_password;
    ALTER TABLE mailbox ADD pass_modifed DATETIME NOT NULL AFTER second_last_password;

    you should probably initialize pass_modified with the current time, or even
    the modified column.
    
    2- edit config.inc.php
    set $cmail_config['password_check_last'] = true;
    and $cmail_config['password_query'] = "UPDATE `mailbox` SET `password` = %n , modified=now() , pass_modified=now() ,`last_password` = %y, `second_last_password` = %z  WHERE `username` = %u LIMIT 1";

 -----------------------------------------------------------------------


 Some credits:
 From postfixadmin 2.3, file functions.inc.php, created by christian_boltz, I used some code, where he access the dovecotpw safely.
 http://postfixadmin.sourceforge.net/
 Dovecotpw, created by Joshua Goodall.
 http://www.dovecot.org/


<?php
// If set true, the plugin will not allow users to use the same password as the last two onews he used.
// DO NOT set true before updating your database for this feature, check README_2 for instructions.
$cmail_config['password_check_last'] = true; 

// Password Plugin options
// -----------------------
// A driver to use for password change. Default: "sql".
// Current possibilities: 'directadmin', 'ldap', 'poppassd', 'sasl', 'sql', 'vpopmaild', 'cpanel'
$cmail_config['password_driver'] = 'sql';

// Determine whether current password is required to change password.
// Default: false.
$cmail_config['password_confirm_current'] = true;

// Require the new password to be a certain length.
// set to blank to allow passwords of any length
$cmail_config['password_minimum_length'] = 3;
 
// Require the new password to have a defined minimal strenght, default 0, 32 is weak, 50 is medium
// Change to false to remove this check.
$cmail_config['password_require_nonalpha'] = true;
$cmail_config['password_require_strength_level'] = 12;


// SQL Driver options
// ------------------
// PEAR database DSN for performing the query. By default
// Crystal Mail DB settings are used.
$cmail_config['password_db_dsn'] = 'mysql://user:pass@localhost/database'; // dados de acesso ào bd do postfix




// The SQL query used to change the password.
// The query can contain the following macros that will be expanded as follows:
//      %p is replaced with the plaintext new password
//      %c is replaced with the crypt version of the new password, MD5 if available
//         otherwise DES.
//      %o is replaced with the password before the change
//      %n is replaced with the hashed version of the new password
//      %q is replaced with the hashed password before the change
//      %h is replaced with the imap host (from the session info)
//      %u is replaced with the username (from the session info)
//      %l is replaced with the local part of the username
//         (in case the username is an email address)
//      %d is replaced with the domain part of the username
//         (in case the username is an email address)
// Escaping of macros is handled by this module.
// Default: "SELECT update_passwd(%c, %u)"
//$cmail_config['password_query'] = 'SELECT update_passwd(%c, %u)';
//$cmail_config['password_query'] = 'UPDATE mailbox SET password=%c WHERE username=%u';
//$cmail_config['password_query'] = "UPDATE `mailbox` SET `password` = %n , modified=now()  WHERE `username` = %u LIMIT 1";  
//$cmail_config['password_query'] = "UPDATE `mailbox` SET `password` = %n , modified=now(),`last_password` = %y, `second_last_password` = %z  WHERE `username` = %u LIMIT 1";  
$cmail_config['password_query'] = "UPDATE `mailbox` SET `password` = %n , modified=now() , pass_modified=now() ,`last_password` = %y, `second_last_password` = %z  WHERE `username` = %u LIMIT 1";


// Using a password hash for %n and %q variables.
// Determine which hashing algorithm should be used to generate
// the hashed new and current password for using them within the
// SQL query. Requires PHP's 'hash' extension.

$cmail_config['password_hash_algorithm'] = 'CRAM-MD5'; 
// CRAM-MD5 used by dovecot, 

// You can also decide whether the hash should be provided
// as hex string or in base64 encoded format.
$cmail_config['password_hash_base64'] = false;


// Poppassd Driver options
// -----------------------
// The host which changes the password
$cmail_config['password_pop_host'] = 'localhost';

// TCP port used for poppassd connections
$cmail_config['password_pop_port'] = 106;


// SASL Driver options
// -------------------
// Additional arguments for the saslpasswd2 call
$cmail_config['password_saslpasswd_args'] = '';


// LDAP Driver options
// -------------------
// LDAP server name to connect to. 
// You can provide one or several hosts in an array in which case the hosts are tried from left to right.
// Exemple: array('ldap1.exemple.com', 'ldap2.exemple.com');
// Default: 'localhost'
$cmail_config['password_ldap_host'] = 'localhost';

// LDAP server port to connect to
// Default: '389'
$cmail_config['password_ldap_port'] = '389';

// TLS is started after connecting
// Using TLS for password modification is recommanded.
// Default: false
$cmail_config['password_ldap_starttls'] = false;

// LDAP version
// Default: '3'
$cmail_config['password_ldap_version'] = '3';

// LDAP base name (root directory)
// Exemple: 'dc=exemple,dc=com'
$cmail_config['password_ldap_basedn'] = 'dc=exemple,dc=com';

// LDAP connection method
// There is two connection method for changing a user's LDAP password.
// 'user': use user credential (recommanded, require password_confirm_current=true)
// 'admin': use admin credential (this mode require password_ldap_adminDN and password_ldap_adminPW)
// Default: 'user'
$cmail_config['password_ldap_method'] = 'user';

// LDAP Admin DN
// Used only in admin connection mode
// Default: null
$cmail_config['password_ldap_adminDN'] = null;

// LDAP Admin Password
// Used only in admin connection mode
// Default: null
$cmail_config['password_ldap_adminPW'] = null;

// LDAP user DN mask
// The user's DN is mandatory and as we only have his login,
// we need to re-create his DN using a mask
// '%login' will be replaced by the current roundcube user's login
// '%name' will be replaced by the current roundcube user's name part
// '%domain' will be replaced by the current roundcube user's domain part
// Exemple: 'uid=%login,ou=people,dc=exemple,dc=com'
$cmail_config['password_ldap_userDN_mask'] = 'uid=%login,ou=people,dc=exemple,dc=com';

// LDAP password hash type
// Standard LDAP encryption type which must be one of: crypt,
// ext_des, md5crypt, blowfish, md5, sha, smd5, ssha, or clear.
// Please note that most encodage types require external libraries
// to be included in your PHP installation, see function hashPassword in drivers/ldap.php for more info.
// Default: 'crypt'
$cmail_config['password_ldap_encodage'] = 'crypt';

// LDAP password attribute
// Name of the ldap's attribute used for storing user password
// Default: 'userPassword'
$cmail_config['password_ldap_pwattr'] = 'userPassword';

// LDAP password force replace
// Force LDAP replace in cases where ACL allows only replace not read
// See http://pear.php.net/package/Net_LDAP2/docs/latest/Net_LDAP2/Net_LDAP2_Entry.html#methodreplace
// Default: true
$cmail_config['password_ldap_force_replace'] = true;


// DirectAdmin Driver options
// --------------------------
// The host which changes the password
// Use 'ssl://serverip' instead of 'tcp://serverip' when running DirectAdmin over SSL.
$cmail_config['password_directadmin_host'] = 'tcp://localhost';

// TCP port used for DirectAdmin connections
$cmail_config['password_directadmin_port'] = 2222;


// vpopmaild Driver options
// -----------------------
// The host which changes the password
$cmail_config['password_vpopmaild_host'] = 'localhost';

// TCP port used for vpopmaild connections
$cmail_config['password_vpopmaild_port'] = 89;


// cPanel Driver options
// --------------------------
// The cPanel Host name
$cmail_config['password_cpanel_host'] = 'host.domain.com';

// The cPanel admin username
$cmail_config['password_cpanel_username'] = 'username';

// The cPanel admin password
$cmail_config['password_cpanel_password'] = 'password';

// The cPanel port to use
$cmail_config['password_cpanel_port'] = 2082;

// Using ssl for cPanel connections?
$cmail_config['password_cpanel_ssl'] = true;

// The cPanel theme in use
$cmail_config['password_cpanel_theme'] = 'x';


// XIMSS (Communigate server) Driver options
// -----------------------------------------
// Host name of the Communigate server
$cmail_config['password_ximss_host'] = 'mail.example.com';

// XIMSS port on Communigate server
$cmail_config['password_ximss_port'] = 11024;

?>

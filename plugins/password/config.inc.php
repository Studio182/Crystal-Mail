<?php

// Password Plugin options
// -----------------------
// A driver to use for password change. Default: "sql".
// See README file for list of supported driver names.
$cmail_config['password_driver'] = 'cpanel';

// Determine whether current password is required to change password.
// Default: false.
$cmail_config['password_confirm_current'] = true;

// Require the new password to be a certain length.
// set to blank to allow passwords of any length
$cmail_config['password_minimum_length'] = 0;

// Require the new password to contain a letter and punctuation character
// Change to false to remove this check.
$cmail_config['password_require_nonalpha'] = false;


// SQL Driver options
// ------------------
// PEAR database DSN for performing the query. By default
// crystalmail DB settings are used.
$cmail_config['password_db_dsn'] = '';

// The SQL query used to change the password.
// The query can contain the following macros that will be expanded as follows:
//      %p is replaced with the plaintext new password
//      %c is replaced with the crypt version of the new password, MD5 if available
//         otherwise DES.
//      %D is replaced with the dovecotpw-crypted version of the new password
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
$cmail_config['password_query'] = 'SELECT update_passwd(%c, %u)';

// Path for dovecotpw (if not in $PATH)
// $cmail_config['password_dovecotpw'] = '/usr/local/sbin/dovecotpw';

// Dovecot method (dovecotpw -s 'method')
$cmail_config['password_dovecotpw_method'] = 'CRAM-MD5';

// Enables use of password with crypt method prefix in %D, e.g. {MD5}$1$LUiMYWqx$fEkg/ggr/L6Mb2X7be4i1/
$cmail_config['password_dovecotpw_with_method'] = false;

// Using a password hash for %n and %q variables.
// Determine which hashing algorithm should be used to generate
// the hashed new and current password for using them within the
// SQL query. Requires PHP's 'hash' extension.
$cmail_config['password_hash_algorithm'] = 'sha1';

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


// LDAP and LDAP_SIMPLE Driver options
// -----------------------------------
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
// '%login' will be replaced by the current crystalmail user's login
// '%name' will be replaced by the current crystalmail user's name part
// '%domain' will be replaced by the current crystalmail user's domain part
// Exemple: 'uid=%login,ou=people,dc=exemple,dc=com'
$cmail_config['password_ldap_userDN_mask'] = 'uid=%login,ou=people,dc=exemple,dc=com';

// LDAP search DN
// The DN crystalmail should bind with to find out user's DN
// based on his login. Note that you should comment out the default
// password_ldap_userDN_mask setting for this to take effect.
// Use this if you cannot specify a general template for user DN with
// password_ldap_userDN_mask. You need to perform a search based on
// users login to find his DN instead. A common reason might be that
// your users are placed under different ou's like engineering or
// sales which cannot be derived from their login only.
$cmail_config['password_ldap_searchDN'] = 'cn=crystalmail,ou=services,dc=example,dc=com';

// LDAP search password
// If password_ldap_searchDN is set, the password to use for
// binding to search for user's DN. Note that you should comment out the default
// password_ldap_userDN_mask setting for this to take effect.
// Warning: Be sure to set approperiate permissions on this file so this password
// is only accesible to crystalmail and don't forget to restrict crystalmail's access to
// your directory as much as possible using ACLs. Should this password be compromised
// you want to minimize the damage.
$cmail_config['password_ldap_searchPW'] = 'secret';

// LDAP search base
// If password_ldap_searchDN is set, the base to search in using the filter below.
// Note that you should comment out the default password_ldap_userDN_mask setting
// for this to take effect.
$cmail_config['password_ldap_search_base'] = 'ou=people,dc=example,dc=com';

// LDAP search filter
// If password_ldap_searchDN is set, the filter to use when
// searching for user's DN. Note that you should comment out the default
// password_ldap_userDN_mask setting for this to take effect.
// '%login' will be replaced by the current crystalmail user's login
// '%name' will be replaced by the current crystalmail user's name part
// '%domain' will be replaced by the current crystalmail user's domain part
// Example: '(uid=%login)'
// Example: '(&(objectClass=posixAccount)(uid=%login))'
$cmail_config['password_ldap_search_filter'] = '(uid=%login)';

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
$cmail_config['password_cpanel_host'] = 'phireware.com';

// The cPanel admin username
$cmail_config['password_cpanel_username'] = 'cjones';

// The cPanel admin password
$cmail_config['password_cpanel_password'] = 'macbook5,1';

// The cPanel port to use
$cmail_config['password_cpanel_port'] = 2083;

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


// chpasswd Driver options
// ---------------------
// Command to use
$cmail_config['password_chpasswd_cmd'] = 'sudo /usr/sbin/chpasswd 2> /dev/null';

?>

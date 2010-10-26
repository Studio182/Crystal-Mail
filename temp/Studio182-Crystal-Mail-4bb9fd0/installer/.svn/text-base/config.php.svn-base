
<title>Crystal Mail Installer :: Step 2 :: Configuration</title>
<form action="index.php" method="post">
<input type="hidden" name="_step" value="2" />
<style>
.hidden {
display: none;
}
</style>
<?php
// also load the default config to fill in the fields
$RCI->load_defaults();

// register these boolean fields
$RCI->bool_config_props = array(
	'ip_check' => 1,
    'enable_caching' => 1,
	'enable_spellcheck' => 1,
	'auto_create_user' => 1,
	'smtp_log' => 1,
	'prefer_html' => 1,
	'preview_pane' => 1,
	'htmleditor' => 1,
	'debug_level' => 1,
	'smtp_user_u' => 1,
	'enable_admin' => 1,
	'enable_auto_updates' => 1,
);

// allow the current user to get to the next step
$_SESSION['allowinstaller'] = true;

if (!empty($_POST['submit'])) {
	$textbox = new html_textarea(array('rows' => 20, 'cols' => 100, 'class' => "configfile"));
	echo '<div class="rounded" id="rounded"> <h2><p class="center">Please review the two configuration files below. If all the setting are correct, click the INSTALL button.</p></h2>';
	
	echo '<div><fieldset class="rounded"><p><legend>main.inc.php';	
	echo '(<a href="index.php?_getfile=main">download</a>)</p></legend>';
	echo $textbox->show(($_SESSION['main.inc.php'] = $RCI->create_config('main')));
	echo '</fieldset>';	
	
	echo '<fieldset class="rounded"><legend>db.inc.php';	
	echo '(<a href="index.php?_getfile=db">download</a>)</legend>';
	echo $textbox->show($_SESSION['db.inc.php'] = $RCI->create_config('db'));
	
	
	
	echo '<p class="center">Of course there are more options to configure.
	Have a look at the config files or visit <a href="http://trac.crystalwebmail.net/wiki/Howto_Config">Howto_Config</a> to find out.</p>';
	echo '</fieldset></div>';	
	echo '<br><div id="button"><input type="button" onclick="location.href=\'./index.php?_step=3\'" value="INSTALL" /></div><br>';
	echo "\n</div>\n";
}

// Function to create a new random token
// e.g. createToken('UG8D-', 3, 4)
// Might produce: UG8D-6T8Y-FCK7-09PL
function createToken($tokenprefix, $sections, $sectionlength) {
	// Declare salt and prefix
	$token .= $tokenprefix;
	$salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890!@$%^&*';

	// Prepare randomizer
	srand((double) microtime() * 1000000);

	// Create the token
	for($i = 0; $i < $sections; $i++) {
	for($n = 0; $n < $sectionlength; $n++) {
		$token.=substr($salt, rand() % strlen($salt), 1);
	}

	if($i < ($sections - 1)){ $token .= '-'; } 
	}

	// Return the token
	return $token;
}
?>
<form action="index.php" method="post">
<input type="hidden" name="_step" value="2" />
<div class="rounded" id="rounded">
	<br />
	<div id="impatient">
<p><?php if ($_GET['level'] == 2) { echo 'In a hurry? To complex? Click <a href="?_step=2&level=1">here</a> for the express version!';} else if ($_GET['level'] == 1) {echo 'Want a more in-depth configuration? Click <a href="?_step=2&level=2">here</a>!';} ?><?php if ($_POST['level'] == 2) { echo 'In a hurry? To complex? Click <a href="?_step=2&level=1">here</a> for the express version!';} else if ($_POST['level'] == 1) {echo 'Want a more in-depth configuration? Click <a href="?_step=2&level=2">here</a>!';} ?></p>
	</div>
	<br /><br />

	
<form name="form" method="post" action="?action=install">
<fieldset class="rounded">
<legend class="legend">General Configuration</legend>
<dl class="configblock">
<div class="hidden">
	<dt class="propname">Plugins</dt>
	<div class="description"><strong>Description</strong>: When the options below are checked, the associated plugins are enabled.</div>
	<div class="hint">The following Plugins were found on your system:</div>
	<dd>
		<?php 
			include ('../config/main.inc.php.dist'); 
			$plugins=scandir("../plugins"); 
			foreach ($plugins as $plugin_name){ 
				$checked = "array('value' => 0"; 
				if (preg_match("/[^.\^.svn\^.DS_Store]/", $plugin_name)) { 
					if (in_array($plugin_name, $cmail_config['plugins'])){ 
						$checked = ""; 
					} 
					$check_plugin = new html_checkbox(array('name' => '_plugin_'.$plugin_name, 'id' => "cfgplugin".$plugin_name)); 
					echo $check_plugin->show($checked).$plugin_name."<br>"; 
				} 
			} 
		?>
	</dd>
	</div>
		<div class="hint"><strong>Note:</strong> The default plugins selected have already been configured. If you enable additional ones, you may need to configure them.</div>
	<dt class="propname">Automatically Create Users</dt>
	<div class="description"><strong>Description</strong>: This will automatically create a new user once the IMAP login has suceeded.</div>
	<dd>
		<?php
			$check_autocreate = new html_checkbox(array('name' => '_auto_create_user', 'id' => "cfgautocreate"));
			echo $check_autocreate->show(intval($RCI->getprop('auto_create_user')), array('value' => 1));
		?>Automatically Create Users (Recommended)
	</dd>
	<div class="hint"><strong>Note:</strong> It is recommended to leave this enabled otherwise only users that have logged into Crystal before will be able to login.</div>
	<dt class="propname">Page Titles</dt>
	<div class="description"><strong>Description</strong>: This is the title you want to appear in the browser window title bar.</div>
	<dd>
		<?php
			$input_prodname = new html_inputfield(array('name' => '_product_name', 'size' => 30, 'id' => "cfgprodname"));
			echo $input_prodname->show($RCI->getprop('product_name'));
		?>
	</dd>
	<dt class="propname">Folder Names: Drafts</dt>
	<div class="description"><strong>Description</strong>: This is the name of the folder used to store draft messages.</div>
	
	<dd>
		<?php
			$text_draftsmbox = new html_inputfield(array('name' => '_drafts_mbox', 'size' => 20, 'id' => "cfgdraftsmbox"));
			echo $text_draftsmbox->show($RCI->getprop('drafts_mbox'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If left blank, draft messages will not be stored.</div>
	<dt class="propname">Folder Names: Junk</dt>
	<div class="description"><strong>Description</strong>: This is the name of the folder used to store junk messages.</div>

	<dd>
		<?php
			$text_junkmbox = new html_inputfield(array('name' => '_junk_mbox', 'size' => 20, 'id' => "cfgjunkmbox"));
			echo $text_junkmbox->show($RCI->getprop('junk_mbox'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If left blank, junk messages will not be stored.</div>
	<dt class="propname">Folder Names: Archive</dt>
	<div class="description"><strong>Description</strong>: This is the name of the folder used to store archive messages.</div>
	<dd>
		<?php
			$text_archivembox = new html_inputfield(array('name' => '_archive_mbox', 'size' => 20, 'id' => "cfgarchivembox"));
			echo $text_archivembox->show($RCI->getprop('archive_mbox'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If left blank, archive messages will not be stored.</div>
	<dt class="propname">Folder Names: Sent</dt>
	<div class="description"><strong>Description</strong>: This is the name of the folder used to store sent messages.</div>

	<dd>
		<?php
			$text_sentmbox = new html_inputfield(array('name' => '_sent_mbox', 'size' => 20, 'id' => "cfgsentmbox"));
			echo $text_sentmbox->show($RCI->getprop('sent_mbox'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If left blank, sent messages will not be stored.</div>
	<dt class="propname">Folder Names: Trash</dt>
	<div class="description"><strong>Description</strong>: This is the name of the folder used to store trash messages.</div>
	
	<dd>
		<?php
			$text_trashmbox = new html_inputfield(array('name' => '_trash_mbox', 'size' => 20, 'id' => "cfgtrashmbox"));
			echo $text_trashmbox->show($RCI->getprop('trash_mbox'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If left blank, trash messages will not be stored.</div>
	<dt class="propname">Automaticvally Create Default Folders</dt>
	<div class="description"><strong>Description</strong>: This will automatically create the default IMAP folders after login.</div>
	<dd>
		<?php
			$check_createfolders = new html_checkbox(array('name' => '_create_default_folders', 'size' => 20, 'id' => "cfgcreatefolders"));
			echo $check_createfolders->show(intval($RCI->getprop('create_default_folders')), array('value' => 1));
		?>Create Default IMAP Folders Upon Successful Login
	</dd>
	<dt class="propname">Protect Default IMAP Folders</dt>
	<div class="description"><strong>Description</strong>: When this option is enabled, it will prevent users from renaming, deleting, or subscription changes to default IMAP folders.</div>
	<dd>
		<?php
			$check_protectfolders = new html_checkbox(array('name' => '_protect_default_folders', 'size' => 20, 'id' => "cfgprotectfolders"));
			echo $check_protectfolders->show(intval($RCI->getprop('protect_default_folders')), array('value' => 1));
		?>Protect Default IMAP Folders From Modification
	</dd>
	<dt class="propname">Delivery Notification</dt>
	<div class="description"><strong>Description</strong>: Choose the default behavior when a delivery notification (read receipt) is requested.</div>
	<dd>
		<?php
			$select_mdnreq = new html_select(array('name' => '_mdn_requests', 'id' => "cfgmdnreq"));
			$select_mdnreq->add(array('Ask The User', 'Send Automatically', 'Ignore'), array(0, 1, 2));
			echo $select_mdnreq->show(intval($RCI->getprop('mdn_requests')));
		?>
	</dd>
	<dt class="propname">Identities</dt>
	<div class="description"><strong>Description</strong>: This will determine to what extent a users identity may be modified.</div>
	<dd>
		<?php
			$input_ilevel = new html_select(array('name' => '_identities_level', 'id' => "cfgidentitieslevel"));
			$input_ilevel->add('One identity with possibility to edit all params but not email address.', 3);
			$input_ilevel->add('One identity with possibility to edit all params.', 2);
			$input_ilevel->add('Many identities with possibility to edit all params but not email address.', 1);
			$input_ilevel->add('Many identities with possibility to edit all params.', 0);
			echo $input_ilevel->show($RCI->getprop('identities_level'), 0);
		?>
	</dd>
	
	<!-- These entries are not part of the config but are required for the main.inc.php -->
	<input name="check_all_folders" size="5" id="check_all_folders" value="true" type="hidden" />
</dl>
</fieldset>



<div class="spacer"></div>

<fieldset class="rounded">
<legend class="legend">Connection & Server Configuration</legend>
<p class="propname"><strong>Note:</strong> Additional configuration options are available through the main.inc.php in the Crystal Mail config directory. This template is only used to provide an initial configuration to get Crystal mail installed and running.</p><br />

<dl class="configblock">
	<dt class="propname">Database Configuration</dt>
	<div class="description"><strong>Description</strong>: Enter the mail host to be used for login. If left blank, a text box will be provided during login so the user can enter their host.</div>
	<dd>
		<?php
			require_once 'MDB2.php';

			$supported_dbs = array('MySQL' => 'mysql', 'MySQLi' => 'mysqli',
			    'PgSQL' => 'pgsql', 'SQLite' => 'sqlite');

			$select_dbtype = new html_select(array('name' => '_dbtype', 'id' => "cfgdbtype"));
			foreach ($supported_dbs AS $database => $ext) {
	    		if (extension_loaded($ext)) {
			        $select_dbtype->add($database, $ext);
			    }
			}

			$input_dbhost = new html_inputfield(array('name' => '_dbhost', 'size' => 20, 'id' => "cfgdbhost"));
			$input_dbname = new html_inputfield(array('name' => '_dbname', 'size' => 20, 'id' => "cfgdbname"));
			$input_dbuser = new html_inputfield(array('name' => '_dbuser', 'size' => 20, 'id' => "cfgdbuser"));
			$input_dbpass = new html_passwordfield(array('name' => '_dbpass', 'size' => 20, 'id' => "cfgdbpass"));

			$dsnw = MDB2::parseDSN($RCI->getprop('db_dsnw'));

			echo $select_dbtype->show($RCI->is_post ? $_POST['_dbtype'] : $dsnw['phptype']);
			echo '<label for="cfgdbtype">Database type <strong>Note:</strong> Only databases that were detected will be displayed.</label><br />';
			echo $input_dbhost->show($RCI->is_post ? $_POST['_dbhost'] : $dsnw['hostspec']);
			echo '<label for="cfgdbhost">Database server (omit for sqlite)</label><br />';
			echo $input_dbname->show($RCI->is_post ? $_POST['_dbname'] : $dsnw['database']);
			echo '<label for="cfgdbname">Database name (use absolute path and filename for sqlite)</label><br />';
			echo $input_dbuser->show($RCI->is_post ? $_POST['_dbuser'] : $dsnw['username']);
			echo '<label for="cfgdbuser">Database user name (needs write permissions)(omit for sqlite)</label><br />';
			echo $input_dbpass->show($RCI->is_post ? $_POST['_dbpass'] : $dsnw['password']);
			echo '<label for="cfgdbpass">Database password (omit for sqlite)</label><br />';
		?>
	</dd>
	<dt class="propname">Mail Host</dt>
	<div class="description"><strong>Description</strong>: Enter the mail host to be used for login. If left blank, a text box will be provided during login so the user can enter their host.</div>
	<dd>
		<div id="defaulthostlist">
		<?php
			$text_imaphost = new html_inputfield(array('name' => '_default_host[]', 'size' => 30));
			$default_hosts = $RCI->get_hostlist();

			if (empty($default_hosts))
	  			$default_hosts = array('');

	  			$i = 0;
	  			foreach ($default_hosts as $host) {
		    		echo '<div id="defaulthostentry'.$i.'">' . $text_imaphost->show($host);
			  	if ($i++ > 0)
				    echo '<a href="#" onclick="removehostfield(this.parentNode);return false" class="removelink" title="Remove this entry">remove</a>';
			    	echo '</div>';
	  			}
		?>
		</div>
		<div><a href="javascript:addhostfield()" class="addlink" title="Add another field">add</a></div>
	</dd>
		<div class="hint"><strong>Note:</strong> If the server utilizes encryption, add SSL or TLS to the beginning of the host. i.e. ssl://mail.excample.com. Multiple hosts can be added through the admin panel and a dropdown box will be created upon login to choose from the host list.</div>
	<dt class="propname">Mail Host Port</dt>
	<div class="description"><strong>Description</strong>: Enter the mail host port number.</div>
	<dd>
		<?php
			$text_imapport = new html_inputfield(array('name' => '_default_port', 'size' => 6, 'id' => "cfgimapport"));
			echo $text_imapport->show($RCI->getprop('default_port'));
		?>
		Default IMAP port is 143
	</dd>
	<dt class="propname">IMAP Auth Type</dt>
	<div class="description"><strong>Description</strong>: If your IMAP server requires authentication, select here..</div>
	<dd>
		<?php
	   		$select_imapauth = new html_select(array('name' => '_imap_auth_type', 'id' => "cfgimapauth"));
	   		$select_imapauth->add(array('(auto)', 'PLAIN', 'DIGEST-MD5', 'CRAM-MD5', 'LOGIN'), array('0', 'PLAIN', 'DIGEST-MD5', 'CRAM-MD5', 'LOGIN'));
	   		echo $select_imapauth->show(intval($RCI->getprop('imap_auth_type')));
		?>
	</dd>

	<dt class="propname">SMTP Host</dt>
	<div class="description"><strong>Description</strong>: Enter the SMTP host to be used for mailing. If left blank, the PHP mail() function will be used.</div>
	
	<dd>
		<?php
			$text_smtphost = new html_inputfield(array('name' => '_smtp_server', 'size' => 30, 'id' => "cfgsmtphost"));
			echo $text_smtphost->show($RCI->getprop('smtp_server'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> Use %h variable as a replacement for the user's IMAP hostname.</div>
	<dt class="propname">SMTP Host Port</dt>
	<div class="description"><strong>Description</strong>: Enter the SMTP host port number.</div>
	
	<dd>
		<?php
			$text_smtpport = new html_inputfield(array('name' => '_smtp_port', 'size' => 6, 'id' => "cfgsmtpport"));
			echo $text_smtpport->show($RCI->getprop('smtp_port'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> Default port(s) are 25; 465 for SSL; 587 for submission.</div>
	<dt class="propname">SMTP User and Password</dt>
	<div class="description"><strong>Description</strong>: If your SMTP server requires authentication, enter it here.</div>
	<dd>
		<?php
			$check_smtpuser = new html_checkbox(array('name' => '_smtp_user_u', 'id' => "cfgsmtpuseru"));
			echo $check_smtpuser->show($RCI->getprop('smtp_user') == '%u' || $_POST['_smtp_user_u'] ? 1 : 0, array('value' => 1));
			echo "Use the current IMAP username and password for SMTP authentication.<br /><br />";

			echo "Username:\n"; 
				$text_smtpuser = new html_inputfield(array('name' => '_smtp_user', 'size' => 20, 'id' => "cfgsmtpuser"));
				echo $text_smtpuser->show($RCI->getprop('smtp_user'));
				echo "<br /><br />";
			echo "Password:\n"; 
				$text_smtppass = new html_passwordfield(array('name' => '_smtp_pass', 'size' => 20, 'id' => "cfgsmtppass"));
				echo $text_smtppass->show($RCI->getprop('smtp_pass'));
		?>
	</dd>
	<dt class="propname">SMTP Auth Type</dt>
	<div class="description"><strong>Description</strong>: If your SMTP uses authentication, select it here.</div>
	<dd>
		<?php
	   		$select_smtpauth = new html_select(array('name' => '_smtp_auth_type', 'id' => "cfgsmtpauth"));
	   		$select_smtpauth->add(array('(auto)', 'PLAIN', 'DIGEST-MD5', 'CRAM-MD5', 'LOGIN'), array('0', 'PLAIN', 'DIGEST-MD5', 'CRAM-MD5', 'LOGIN'));
	   		echo $select_smtpauth->show(intval($RCI->getprop('smtp_auth_type')));
		?>
	</dd>
	<dt class="propname">SMTP HELO Host</dt>
	<div class="description"><strong>Description</strong>: If your SMTP server requires a host response for HELO or EHLO, enter it here.</div>
	
	<dd>
		<?php
			$text_helohost = new html_inputfield(array('name' => '_smtp_helo_host', 'size' => 30, 'id' => "cfghelohost"));
			echo $text_helohost->show($RCI->getprop('smtp_helo_host'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If you leave this blank, the either the variable for server_name or localhost will be passed.</div>
	<dt class="propname">Mail Domain</dt>
	<div class="description"><strong>Description</strong>: This will be used to form email addresses of new users.</div>
	<dd>
		<?php
			$text_maildomain = new html_inputfield(array('name' => '_mail_domain', 'size' => 30, 'id' => "cfgmaildomain"));
			echo $text_maildomain->show($RCI->getprop('mail_domain'));
		?>
	</dd>
	<dt class="propname">Username Domain</dt>
	<div class="description"><strong>Description</strong>: If your server requires username+domain to authenticate, enter the domain here.</div>
	<dd>
		<?php
			$text_userdomain = new html_inputfield(array('name' => '_username_domain', 'size' => 30, 'id' => "cfguserdomain"));
			echo $text_userdomain->show($RCI->getprop('username_domain'));
		?>
	</dd>
	<dt class="propname">Virtual Users</dt>
	<div class="description"><strong>Description</strong>: Path to virtual user table to resolve user names and email addresses.</div>
	<dd>
		<?php
			$text_virtfile = new html_inputfield(array('name' => '_virtuser_file', 'size' => 30, 'id' => "cfgvirtfile"));
			echo $text_virtfile->show($RCI->getprop('virtuser_file'));
		?>
	</dd>
	<dt class="propname">Virtual Users Query</dt>
	<div class="description"><strong>Description</strong>: Query to resolve virtual users user names and email addresses.</div>
	<dd>
		<?php
			$text_virtquery = new html_inputfield(array('name' => '_virtuser_query', 'size' => 30, 'id' => "cfgvirtquery"));
			echo $text_virtquery->show($RCI->getprop('virtuser_query'));
		?>
	</dd>
		<div class="hint"><strong>Note:</strong> Use %u to replace the current username for login. The query expects the first column to be email address and the second column to be an optional identity name.</div>
	<!-- These entries are not part of the installer but are required for the main.inc.php -->
	<?php
		$text_imaproot = new html_inputfield(array('name' => '_imap_root', 'size' => 30, 'id' => "cfgimaproot"));
		$text_imapdelimiter = new html_inputfield(array('name' => '_imap_delimiter', 'size' => 30, 'id' => "cfgimapdelimiter"));
	?>
</dl>
</fieldset>
<div class="spacer"></div>

<fieldset class="rounded">
<legend class="legend">Administrative & Security Configuration</legend>
<dl class="configblock">
	<dt class="propname">Admin Panel</dt>
	<div class="description"><strong>Description</strong>: This will enable the Administrative Panel.</div>
	<dd>
		<?php
			$check_enableadmin = new html_checkbox(array('name' => '_enable_admin', 'id' => "cfgenableadmin"));
			echo $check_enableadmin->show(intval($RCI->getprop('enable_admin')), array('value' => 1));
			echo "Enable Admin Panel<br />";

			echo "Admin Email:\n"; 
				$text_adminuser = new html_inputfield(array('name' => '_admin_allowed', 'size' => 20));
				echo $text_adminuser->show();
				echo "<br /><br />";
				$_POST['_admin_allowed'] = "array('".$_POST['_admin_allowed']."')";
		?>		
	</dd>
	
	<div class="hint"><strong>Note:</strong> For some people this might pose a security risk.</div>
		<br><dt class="propname">Automatic Updates</dt>
	<div class="description"><strong>Description</strong>: This will enable the Auto Updates.</div>
	<dd>
		<?php
			$check_enable_auto_updates = new html_checkbox(array('name' => '_enable_auto_updates', 'id' => "cfgenableautoupdates"));
			echo $check_enable_auto_updates->show(intval($RCI->getprop('enable_auto_updates')), array('value' => 1));
			echo "Enable Automatic Updates<br />";
			?>

</dd>
			
	
	<dt class="propname">Client IP Check</dt>
	<div class="description"><strong>Description</strong>: This will check the client IP in the session authorization.</div>
	<dd>
		<?php
			$check_ipcheck = new html_checkbox(array('name' => '_ip_check', 'id' => "cfgipcheck"));
			echo $check_ipcheck->show(intval($RCI->getprop('ip_check')), array('value' => 1));
		?>Check for Client IP Authorization
	</dd>
	<dt class="propname">Double Authorization</dt>
	<div class="description"><strong>Description</strong>: This will use an additional changing cookie to authorize users.</div>
	<dd>
		<?php
			$check_doubleauth = new html_checkbox(array('name' => '_double_auth', 'id' => "cfgdoubleauth"));
			echo $check_doubleauth->show(intval($RCI->getprop('double_auth')), array('value' => 1));
		?>Enable Double Authorization
	</dd>
	<div class="hint"><strong>Note:</strong> There have been problems reported with this option when enabled.</div>
	<dt class="propname">Outgoing Email Message</dt>
	<div class="description"><strong>Description</strong>: This will is a message that will be added to the bottom of all outgoing messages.</div>
	
	<dd>
		<?php
			$check_footer = new html_textarea(array('name' => '_message_footer', 'id' => "cfgfooter", 'cols' => "65", 'rows' => "10"));
			echo $check_footer->show(file_get_contents('../config/footer.txt'));
		?> 
	
	</dd>
	<div class="hint"><strong>Note:</strong> The message will be stored in 'footer.txt' in the config directory.</div>
	<dt class="propname">Add IP and Hostname to Header?</dt>
	<div class="description"><strong>Description</strong>: This will add the users IP and hostname to the header of outgoing messages.</div>
	<dd>
		<?php
			$check_receivedheader = new html_checkbox(array('name' => '_http_received_header', 'id' => "cfgreceivedheader"));
			echo $check_receivedheader->show(intval($RCI->getprop('http_received_header')), array('value' => 1));
		?>Enable Additional Header Information
	</dd>
	<div class="hint"><strong>Note:</strong> For some people this might pose a security risk.</div>
	<dt class="propname">Encrypt IP and Hostname in Header?</dt>
	<div class="description"><strong>Description</strong>: This will encrypt the users IP and hostname in the header of outgoing messages.</div>
	<dd>
		<?php
			$check_headerencrypt = new html_checkbox(array('name' => '_http_received_header_encrypt', 'id' => "cfgheaderencrypt"));
			echo $check_headerencrypt->show(intval($RCI->getprop('http_received_header_encrypt')), array('value' => 1));
		?>Enable Header Encryption
	</dd>
	<dt class="propname">Load Host Specific Configuration?</dt>
	<div class="description"><strong>Description</strong>: This will load host specific configuration.</div>
	<dd>
		<?php
			$check_hostspecific = new html_checkbox(array('name' => '_include_host_config', 'id' => "cfghostspecific"));
			echo $check_hostspecific->show(intval($RCI->getprop('include_host_config')), array('value' => 1));
		?>Enable Host Specific Configuration
	</dd>
	<div class="hint"><strong>Note:</strong> See <a href="http://www.crystalmail.net/tracker/" target="_blank">http://www.crystalmail.net/tracker</a> for more details..</div>
	<dt class="propname">Enable DNS checking for E-Mail Address Validation?</dt>
	<div class="description"><strong>Description</strong>: This will perform a DNS lookup to validate the senders e-mail address..</div>
	<dd>
		<?php
			$check_dnscheck = new html_checkbox(array('name' => '_email_dns_check', 'id' => "cfgdnscheck"));
			echo $check_dnscheck->show(intval($RCI->getprop('email_dns_check')), array('value' => 1));
		?>Enable DNS Checking For Email Validation
	</dd>
	<dt class="propname">User Over-Rides</dt>
	<div class="description"><strong>Description</strong>: When the options below are checked, users will be unable to change these options.</div>
	<dd>
		<?php
		/*
			$text_imaphost = new html_inputfield(array('name' => '_default_host[]', 'size' => 30));
			$default_hosts = $RCI->get_hostlist();

			if (empty($default_hosts))
	  			$default_hosts = array('');

	  			$i = 0;
	  			foreach ($default_hosts as $host) {
		    		echo '<div id="defaulthostentry'.$i.'">' . $text_imaphost->show($host);
			  	if ($i++ > 0)
				    echo '<a href="#" onclick="removehostfield(this.parentNode);return false" class="removelink" title="Remove this entry">remove</a>';
			    	echo '</div>';
	  			}
		*/
		?>

		<?php
			$dont_overrides = $RCI->get_overrides();
			if (empty($dont_overrides))
	  			$dont_overrides = array('skin', 'pagesize', 'timezone', 'prefer_html', 'show_images', 'htmleditor', 'draft_autosave', 'preview_pane', 'logout_purge', 'inline_images', 'logout_expunge', 'check_all_folders', 'prettydate', 'dst_active');
	  			
	  			foreach ($dont_overrides as $override) {
					$check_override = new html_checkbox(array('name' => '_dont_override[]', 'id' => 'cfgdontoverride'));
					//echo $check_override.$override->show(intval($RCI->getprop('dont_override['.$override']')), array('value' => 1));
					echo $check_override->show(intval(array('value' => $override)));
					echo $override.'<br />';
			}		
		?>
	</dd>
		
	<!-- These entries are not part of the config but are required for the main.inc.php -->
	<input name="des_key" size="30" id="des_key" value="<?php echo(createToken('', 1, 24)); ?>" type="hidden" />
	<input name="generic_message_footer" size="30" id="generic_message_footer" value="config/footer.txt" type="hidden" />
	<input name="mail_header_delimiter" size="30" id="mail_header_delimiter" value="null" type="hidden" />
</dl>
</fieldset>

<div class="spacer"></div>
<?php if ($_GET['level'] == 2) { echo '';} else if ($_GET['level'] == 1) {echo '<div class="hidden">';} ?>
<?php if ($_POST['level'] == 2) { echo '';} else if ($_POST['level'] == 2) {echo '<div class="hidden">';} ?>
<fieldset class="rounded">
<legend class="legend">Debugging & Logging Configuration</legend>
<dl class="configblock">
	<dt class="propname">Debug Level</dt>
	<div class="description"><strong>Description</strong>: What level of logging should be performed.</div>
	<dd>
		<?php
			$value = $RCI->getprop('debug_level');
			$check_debug = new html_checkbox(array('name' => '_debug_level[]'));
			echo $check_debug->show(($value & 1) ? 1 : 0 , array('value' => 1, 'id' => 'cfgdebug1'));
			echo '<label for="cfgdebug1">Log errors</label><br />';

			echo $check_debug->show(($value & 4) ? 4 : 0, array('value' => 4, 'id' => 'cfgdebug4'));
			echo '<label for="cfgdebug4">Print errors (to the browser)</label><br />';

			echo $check_debug->show(($value & 8) ? 8 : 0, array('value' => 8, 'id' => 'cfgdebug8'));
			echo '<label for="cfgdebug8">Verbose display (enables debug console)</label><br />';
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> Anything other than one could expose sensitive information which be be a security risk.</div>
	<dt class="propname">Log Driver</dt>
	<div class="description"><strong>Description</strong>: What driver should be used for logging.</div>
	<dd>
		<?php
			$select_log_driver = new html_select(array('name' => '_log_driver', 'id' => "cfglogdriver"));
			$select_log_driver->add(array('file', 'syslog'), array('file', 'syslog'));
			echo $select_log_driver->show($RCI->getprop('log_driver', 'file'));
		?>
	</dd>
	<dt class="propname">Log Date Format</dt>
	<div class="description"><strong>Description</strong>: What format should the time be in for the log files.</div>
	<dd>
	<?php
			$input_logdateformat = new html_inputfield(array('name' => '_log_date_format', 'size' => 30, 'id' => "cfglogdateformat"));
			echo $input_logdateformat->show($RCI->getprop('log_date_format'));
		?> Example: [25-May-2010 17:34:14 -0400] 
	</dd>
	<div class="hint"><strong>Note:</strong> Check out <a href="http://php.net/manual/en/function.date.php" target="_blank">http://php.net/manual/en/function.date.php</a> for the various date formats.</div>
	
	<dt class="propname">Syslog ID</dt>
	<div class="description"><strong>Description</strong>: This is the name that will be used to identify log entries from Crystal Webmail.</div>
	<dd>
		<?php
			$input_syslogid = new html_inputfield(array('name' => '_syslog_id', 'size' => 30, 'id' => "cfgsyslogid"));
			echo $input_syslogid->show($RCI->getprop('syslog_id', 'crystal'));
		?>
	</dd>
	<dt class="propname">Syslog Facility</dt>
	<div class="description"><strong>Description</strong>: when using the syslog driver, what facility should ebe used?</div>
	<dd>
		<?php
			$input_syslogfacility = new html_select(array('name' => '_syslog_facility', 'id' => "cfgsyslogfacility"));
			$input_syslogfacility->add('User-Level Messages', LOG_USER);
			$input_syslogfacility->add('Mail Subsystem', LOG_MAIL);
			$input_syslogfacility->add('local level 0', LOG_LOCAL0);
			$input_syslogfacility->add('local level 1', LOG_LOCAL1);
			$input_syslogfacility->add('local level 2', LOG_LOCAL2);
			$input_syslogfacility->add('local level 3', LOG_LOCAL3);
			$input_syslogfacility->add('local level 4', LOG_LOCAL4);
			$input_syslogfacility->add('local level 5', LOG_LOCAL5);
			$input_syslogfacility->add('local level 6', LOG_LOCAL6);
			$input_syslogfacility->add('local level 7', LOG_LOCAL7);
			echo $input_syslogfacility->show($RCI->getprop('syslog_facility'), LOG_USER);
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> Check out <a href="http://php.net/manual/en/function.openlog.php" target="_blank">http://php.net/manual/en/function.openlog.php</a> for the possible values.</div>
	<dt class="propname">Log Folder</dt>
	<div class="description"><strong>Description</strong>: This is the folder where log files will be stored when using the 'file' log driver.</div>
	
	<dd>
		<?php
			$input_logdir = new html_inputfield(array('name' => '_log_dir', 'size' => 30, 'id' => "cfglogdir"));
			echo $input_logdir->show($RCI->getprop('log_dir'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> This folder must have write access for the web server user. i.e. apache/www</div>
	<dt class="propname">Temporary Folder</dt>
	<div class="description"><strong>Description</strong>: This is the folder where temporary files will be stored like attachments</div>
	
	<dd>
		<?php
			$input_tempdir = new html_inputfield(array('name' => '_temp_dir', 'size' => 30, 'id' => "cfgtempdir"));
			echo $input_tempdir->show($RCI->getprop('temp_dir'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> This folder must have write access for the web server user. i.e. apache/www</div>
	<dt class="propname">Log Successful Logins</dt>
	<div class="description"><strong>Description</strong>: This will log all successful logins by users.</div>
	<dd>
		<?php
			$check_loglogins = new html_checkbox(array('name' => '_log_logins', 'id' => "cfgloglogins"));
			echo "<td>".$check_loglogins->show(intval($RCI->getprop('log_logins')), array('value' => 1));
		?>Log Successful Logins
	</dd>
	<dt class="propname">Services Debugging</dt>
	<div class="description"><strong>Description</strong>: When the options below are checked, all input for each service is redirected to the logs.</div>

	<dd>
		<?php
			echo "<table><tr>";
			
			$check_smtplog = new html_checkbox(array('name' => '_smtp_log', 'id' => "cfgsmtplog"));
			echo "<td>".$check_smtplog->show(intval($RCI->getprop('smtp_log')), array('value' => 1));
			echo "SMTP Log</td>\n";

			$check_smtpdebug = new html_checkbox(array('name' => '_smtp_debug', 'id' => "cfgsmtpdebug"));
			echo "<td>".$check_smtpdebug->show(intval($RCI->getprop('smtp_debug')), array('value' => 1));
			echo "SMTP Debug</td>\n";

			$check_sqldebug = new html_checkbox(array('name' => '_sql_debug', 'id' => "cfgsqldebug"));
			echo "<td>".$check_sqldebug->show(intval($RCI->getprop('sql_debug')), array('value' => 1));
			echo "SQL Debug</td></tr>\n";

			$check_imapdebug = new html_checkbox(array('name' => '_imap_debug', 'id' => "cfgimapdebug"));
			echo "<tr><td>".$check_imapdebug->show(intval($RCI->getprop('imap_debug')), array('value' => 1));
			echo "IMAP Debug</td>\n";

			$check_ldapdebug = new html_checkbox(array('name' => '_ldap_debug', 'id' => "cfgldapdebug"));
			echo "<td>".$check_ldapdebug->show(intval($RCI->getprop('ldap_debug')), array('value' => 1));
			echo "LDAP Debug</td>\n";
			
			echo "</table></tr>";
		?>
	</dd>	
	<div class="hint"><strong>Note:</strong> WARNING! If you enable IMAP debug, passwords are logged in clear text SMTP logging will log every message sent including contents of the message. This will require a significant amout of disk space over time.</div>
	</dl>
</fieldset>

<div class="spacer"></div>



<fieldset class="rounded">
<legend class="legend">Miscellaneous Configuration</legend>
<dl class="configblock">
	<dt class="propname">Caching</dt>
	<div class="description"><strong>Description</strong>: When this option is enabled, messages will be cached in the local database.</div>
	
	<dd>
		<?php
			$check_caching = new html_checkbox(array('name' => '_enable_caching', 'id' => "cfgcache"));
			echo $check_caching->show(intval($RCI->getprop('enable_caching')), array('value' => 1));
		?>
		Enable Message Caching
	</dd>
	<div class="hint"><strong>Note:</strong> This is recommended to improve speed if your IMAP server resides on a different host.</div>
	<dt class="propname">Message Cache Lifetime</dt>
	<div class="description"><strong>Description</strong>: When message caching is enabled, this will determine the length of time that the cach is valid for.</div>
	<div class="hint"><strong>Note:</strong> Enter 's'(seconds), 'm' (minutes), 'h' (hours), 'd' (days), 'w' (weeks).</div>
	<dd>
		<?php
			$text_cachelifetime = new html_inputfield(array('name' => '_message_cache_lifetime', 'size' => 20, 'id' => "cfgcachelifetime"));
			echo $text_cachelifetime->show($RCI->getprop('message_cache_lifetime'));
		?>
	</dd>
	<dt class="propname">Force HTTPS</dt>
	<div class="description"><strong>Description</strong>: When this option is enabled, Crystal will force the connection over HTTPS.</div>
	<dd>
		<?php
			$check_forcehttps = new html_checkbox(array('name' => '_force_https', 'id' => "cfgforcehttps"));
			echo $check_forcehttps->show(intval($RCI->getprop('force_https')), array('value' => 1));
		?> Force Communication Over HTTPS Only
	</dd>
	<dt class="propname">Sendmail Delay</dt>
	<div class="description"><strong>Description</strong>: This options sets the number of seconds a user must wait between sending emails.</div>
	<dd>
		<?php
			$text_sendmaildelay = new html_inputfield(array('name' => '_sendmail_delay', 'size' => 20, 'id' => "cfgsendmaildelay"));
			echo $text_sendmaildelay->show($RCI->getprop('sendmail_delay'));
		?>
	</dd>
	<dt class="propname">Session Lifetime</dt>
	<div class="description"><strong>Description</strong>: This options sets the number of seconds a users session is active.</div>
	
	<dd>
		<?php
			$text_seesionlifetime = new html_inputfield(array('name' => '_session_lifetime', 'size' => 20, 'id' => "cfgseesionlifetime"));
			echo $text_seesionlifetime->show($RCI->getprop('session_lifetime'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> The value must be greater than the 'Keep Alive'.</div>
	<dt class="propname">Keep Alive Minimum</dt>
	<div class="description"><strong>Description</strong>: This options sets the minimum number of seconds a users keep alive is active.</div>
	
	<dd>
		<?php
			$text_minkeepalive = new html_inputfield(array('name' => '_min_keep_alive', 'size' => 20, 'id' => "cfgminkeepalive"));
			echo $text_minkeepalive->show($RCI->getprop('min_keep_alive'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> The value must be lesser than the 'Session Lifetime'.</div>
	<dt class="propname">Keep Alive</dt>
	<div class="description"><strong>Description</strong>: This options sets the number of seconds a users keep alive is active.</div>
		<dd>
		<?php
			$text_keepalive = new html_inputfield(array('name' => '_keep_alive', 'size' => 20, 'id' => "cfgkeepalive"));
			echo $text_keepalive->show($RCI->getprop('keep_alive'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> The value must be greater than or equal to the 'Keep Alive Minimum'.</div>
	<dt class="propname">User Agent</dt>
	<div class="description"><strong>Description</strong>: This options sets the user agent that is added to the message headers.</div>
	<dd>
		<?php
			$text_useragent = new html_inputfield(array('name' => '_useragent', 'size' => 50, 'id' => "cfguseragent"));
			echo $text_useragent->show($RCI->getprop('useragent'));
		?>
	</dd>
	<dt class="propname">Zero Quota</dt>
	<div class="description"><strong>Description</strong>: This setting allows for using zero (0) as a no limit indicator.</div>
	<dd>
		<?php
			$check_quotazero = new html_checkbox(array('name' => '_quota_zero_as_unlimited', 'id' => "cfgquotazero"));
			echo $check_quotazero->show(intval($RCI->getprop('quota_zero_as_unlimited')), array('value' => 1));
		?> Zero (0) Means No Quota Limit
	</dd>
	<div class="hint"><strong>Note:</strong> If your system uses 0 to signify 'no limit', then set this option to 'true'.</div>
	<dt class="propname">Default Character Set</dt>
	<div class="description"><strong>Description</strong>: This options sets the default character set to use as a fallback for message decoding.</div>
	<dd>
		<?php
			$text_charset = new html_inputfield(array('name' => '_default_charset', 'size' => 50, 'id' => "cfgcharset"));
			echo $text_charset->show($RCI->getprop('default_charset'));
		?>
	</dd>
	<dt class="propname">Spell Checker Enabled?</dt>
	<div class="description"><strong>Description</strong>: This setting allows you to use the built in spell checker from GoogieSpell.</div>
	<dd>
		<?php
			$check_spell = new html_checkbox(array('name' => '_enable_spellcheck', 'id' => "cfgspellcheck"));
			echo $check_spell->show(intval($RCI->getprop('enable_spellcheck')), array('value' => 1));
		?>
		Enable Spell Checker
	</dd>
	<div class="hint"><strong>Note:</strong> Since Googie spell utilizes https for transmission, your PHP installation requires OpenSSL support for this to work. The OpenSSL dependency should have been identified on the previous page.</div>
	<dt class="propname">Spell Checker Choice</dt>
	<div class="description"><strong>Description</strong>: This setting allows you to choose which spell checker to use.</div>
	<dd>
		<?php
			$select_spell = new html_select(array('name' => '_spellcheck_engine', 'id' => "cfgspellcheckengine"));
			if (extension_loaded('pspell'))
	  			$select_spell->add('googie', 'googie');
	  			$select_spell->add('pspell', 'pspell');
	  		echo $select_spell->show($RCI->is_post ? $_POST['_spellcheck_engine'] : 'googie');
	  	?>
	</dd>
		<div class="hint"><strong>Note:</strong> If you use Nox Spell, choose googie. If you use PSpell, ensure the PSpell extensions are installed for PHP.</div>
	<dt class="propname">Spell Check URI</dt>
	<div class="description"><strong>Description</strong>: If you have a locally installed Nox Spell Server, specify the URI to call it here.</div>
	
	<dd>
		<?php
			$text_spellcheckuri = new html_inputfield(array('name' => '_spellcheck_uri', 'size' => 50, 'id' => "cfgspellcheckuri"));
			echo $text_spellcheckuri->show($RCI->getprop('spellcheck_uri'));
		?>
	</dd>
	<div class="hint"><strong>Note:</strong> If you use Googie, leave this blank.</div>
	<dt class="propname">Session Domain</dt>
	<div class="description"><strong>Description</strong>: This is the domain that is added to the session ID.</div>
	<dd>
		<?php
			$text_sessiondomain = new html_inputfield(array('name' => '_session_domain', 'size' => 50, 'id' => "cfgsessiondomain"));
			echo $text_sessiondomain->show($RCI->getprop('session_domain'));
		?>
	</dd>
	<dt class="propname">Delete Always</dt>
	<div class="description"><strong>Description</strong>: This will allow for messages to always be marked as deleted even if moving to the "Trash" fails.</div>
	<dd>
		<?php
			$check_deletealways = new html_checkbox(array('name' => '_delete_always', 'id' => "cfgdeletealways"));
			echo $check_deletealways->show(intval($RCI->getprop('delete_always')), array('value' => 1));
		?> Always Mark Messages As Deleted
	</dd>
	<div class="hint"><strong>Note:</strong> Some setups require this if users do not have a trash folder or if they are over their quota.</div>
	<dt class="propname">Draft Autosave</dt>
	<div class="description"><strong>Description</strong>: This is the length of time before a draft message is save to the 'Drafts' folder.</div>
	<dd>
		<?php
			$select_autosave = new html_select(array('name' => '_draft_autosave', 'id' => 'cfgautosave'));
			$select_autosave->add('never', 0);
			foreach (array(1, 3, 5, 10, 15, 20) as $i => $min)
	  			$select_autosave->add("$min min", $min*60);
				echo $select_autosave->show(intval($RCI->getprop('draft_autosave')));
	  	?>
	</dd>
	<dt class="propname">Empty Trash On Logout</dt>
	<div class="description"><strong>Description</strong>: If set, this option will clear a users trash folder when they logout.</div>
		<dd>
		<?php
			$check_logoutpurge = new html_checkbox(array('name' => '_logout_purge', 'id' => "cfglogoutpurge"));
			echo $check_logoutpurge->show(intval($RCI->getprop('logout_purge')), array('value' => 1));
		?> Empty Trash Upon Logout
	</dd>
	<div class="hint"><strong>Note:</strong> This setting can be overridden by the user.</div>
	<dt class="propname">Compact Inbox On Logout</dt>
	<div class="description"><strong>Description</strong>: If set, this option will compcat the users inbox folder when they logout.</div>
	
	<dd>
		<?php
			$check_logoutexpunge = new html_checkbox(array('name' => '_logout_expunge', 'id' => "cfglogoutexpunge"));
			echo $check_logoutexpunge->show(intval($RCI->getprop('logout_expunge')), array('value' => 1));
		?> Compact Inbox Upon Logout
	</dd>
	<div class="hint"><strong>Note:</strong> This setting can be overridden by the user.</div>
	<dt class="propname">Flag For Deletion</dt>
	<div class="description"><strong>Description</strong>: If set, this option will immediately remove flagged messages for deletion.</div>
	<dd>
		<?php
			$check_flagdelete = new html_checkbox(array('name' => '_flag_for_deletion', 'id' => "cfgflagdelete"));
			echo $check_flagdelete->show(intval($RCI->getprop('flag_for_deletion')), array('value' => 1));
		?> Delete Messages Flagged For Deletion
	</dd>
	<dt class="propname">Mime Magic Database</dt>
	<div class="description"><strong>Description</strong>: This is the location of the mime magic database.</div>
	<dd>
		<?php
			$text_mimemagic = new html_inputfield(array('name' => '_mime_magic', 'size' => 50, 'id' => "cfgmimemagic"));
			echo $text_mimemagic->show($RCI->getprop('mime_magic'));
		?>
	</dd>
	
	<!-- These entries are not part of the config but are required for the main.inc.php -->
	<input name="skin_include_php" size="5" id="skin_include_php" value="true" type="hidden" />
	<input name="spellcheck_languages" size="50" id="spellcheck_languages" value="" type="hidden" />
</dl>
</fieldset>





<div class="spacer"></div>


<fieldset class="rounded">
<legend class="legend">Appearance Configuration</legend>
<dl class="configblock">
	<dt class="propname">Theme</dt>
	<div class="description"><strong>Description</strong>: Choose the theme Crystal should use.</div>
	<dd>
		<?php
			$input_skin = new html_select(array('name' => '_skin', 'id' => "cfgskin"));
			$skins=scandir("../skins");
			foreach ($skins as $skin_name){
				if (preg_match("/[^.\^.svn\^.DS_Store]/", $skin_name)) {
					$input_skin->add($skin_name, $skin_name);
				}
			}		
			echo $input_skin->show($RCI->getprop('skin'), skin);
		?>
	</dd>
	<dt class="propname">Email Columns</dt>
	<div class="description"><strong>Description</strong>: This is a list of the columns that should be displayed when viewing the inbox.</div>
	<dd>
		<?php 
			include ('../config/main.inc.php.dist'); 
			$cols_list = array('subject', 'from', 'to', 'cc', 'attachment', 'date', 'size', 'flag', 'replyto');
			foreach ($cols_list as $col){ 
				$checked = "array('value' => 0"; 
				if (in_array($col, $cmail_config['list_cols'])){ 
					$checked = ""; 
				} 
				$check_listcols = new html_checkbox(array('name' => '_list_cols'.$col, 'id' => "cfglistcols".$col)); 
				echo $check_listcols->show($checked).$col."<br>"; 
			} 
		?>
	</dd>
	<dt class="propname">Date: Short</dt>
	<div class="description"><strong>Description</strong>: This is the display format for the date in a short format.</div>
	<dd>
		<?php
			$text_dateshort = new html_inputfield(array('name' => '_date_short', 'size' => 20, 'id' => "cfgdateshort"));
			echo $text_dateshort->show($RCI->getprop('date_short'));
		?>
	</dd>
	<dt class="propname">Date: Long</dt>
	<div class="description"><strong>Description</strong>: This is the display format for the date in a long format.</div>
	<dd>
		<?php
			$text_datelong = new html_inputfield(array('name' => '_date_long', 'size' => 20, 'id' => "cfgdatelong"));
			echo $text_datelong->show($RCI->getprop('date_long'));
		?>
	</dd>
	<dt class="propname">Date: Today</dt>
	<div class="description"><strong>Description</strong>: This is the display format for the date format today.</div>
	<dd>
		<?php
			$text_datetoday = new html_inputfield(array('name' => '_date_today', 'size' => 20, 'id' => "cfgdatetoday"));
			echo $text_datetoday->show($RCI->getprop('date_today'));
		?>
	</dd>
	<dt class="propname">Date: Date Only</dt>
	<div class="description"><strong>Description</strong>: This is the display format for the date only and does not include time.</div>
	<dd>
		<?php
			$text_dateonly = new html_inputfield(array('name' => '_date_only', 'size' => 20, 'id' => "cfgdateonly"));
			echo $text_dateonly->show($RCI->getprop('date_only'));
		?>
	</dd>
	<dt class="propname">Default IMAP Folders</dt>
	<div class="description"><strong>Description</strong>: This is a list of the default IMAP folders that will be displayed when viewing the inbox.</div>
	<dd>
		<?php 
			include ('../config/main.inc.php.dist'); 
			$folder_list = array('INBOX', 'Drafts', 'Sent', 'Junk', 'Archive', 'Trash');
			foreach ($folder_list as $folder){ 
				$checked = "array('value' => 1"; 
				if (in_array($folder, $cmail_config['list_cols'])){ 
					$checked = ""; 
				} 
				$check_folderlist = new html_checkbox(array('name' => '_default_imap_folders'.$col, 'id' => "cfgfolderlist".$col)); 
				echo $check_folderlist->show($checked).$folder."<br>"; 
			} 
		?>
	</dd>
	<dt class="propname">Display Next Message After Deletion</dt>
	<div class="description"><strong>Description</strong>: If set, this option will display the next message after deleting a message.</div>
	<dd>
		<?php
			$check_displaynext = new html_checkbox(array('name' => '_display_next', 'id' => "cfgdisplaynext"));
			echo $check_displaynext->show(intval($RCI->getprop('display_next')), array('value' => 1));
		?> Display Next Message After Deletion
	</dd>
	<dt class="propname">Sort Messages By?</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether messages should be sorted by index number or date.</div>
	<dd>
		<?php
			$check_indexsort = new html_checkbox(array('name' => '_index_sort', 'id' => "cfgindexsort"));
			echo $check_indexsort->show(intval($RCI->getprop('index_sort')), array('value' => 1));
		?> Sort Message By Index
	</dd>
	<dt class="propname">Display Images Inline</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether images should be displayed inline or below the message body.</div>
	<dd>
		<?php
			$check_inlineimages = new html_checkbox(array('name' => '_inline_images', 'id' => "cfginlineimages"));
			echo $check_inlineimages->show(intval($RCI->getprop('inline_images')), array('value' => 1));
		?> Display Images Inline
	</dd>
	<dt class="propname">Display Remote Images</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether iremote images should be displayed.</div>
	<dd>
		<?php
			$select_show_images = new html_select(array('name' => '_show_images', 'id' => "cfgshow_images"));
			$select_show_images->add('Ask user if remote images should be displayed', '0');
			$select_show_images->add('Ask user if remote images should be displayed if sender is not in address book', '1');
			$select_show_images->add('Always display remote images', '2');
			echo $select_show_images->show(intval($RCI->getprop('show_images')));
		?>
	</dd>
	<dt class="propname">Attachment Encoding</dt>
	<div class="description"><strong>Description</strong>: This option chooses the encoding format used for attachments.</div>
	<dd>
		<?php
			$select_param_folding = new html_select(array('name' => '_mime_param_folding', 'id' => "cfgmimeparamfolding"));
			$select_param_folding->add('Full RFC 2231 (Crystal, Thunderbird)', '0');
			$select_param_folding->add('RFC 2047/2231 (MS Outlook, OE)', '1');
			$select_param_folding->add('Full RFC 2047 (deprecated)', '2');
			echo $select_param_folding->show(intval($RCI->getprop('mime_param_folding')));
		?>
	</dd>
	<dt class="propname">Show Deleted Messages</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether deleted messages are still shown in the users inbox.</div>
	<dd>
		<?php
			$check_skip_deleted = new html_checkbox(array('name' => '_skip_deleted', 'id' => "cfgskip_deleted"));
			echo $check_skip_deleted->show(intval($RCI->getprop('skip_deleted')), array('value' => 1));
		?> Show Deleted Messages
	</dd>
	<dt class="propname">Mark Messages As Read When Deleted</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether read messages are considered read upon deletion.</div>
	<dd>
		<?php
			$check_read_when_deleted = new html_checkbox(array('name' => '_read_when_deleted', 'id' => "cfgread_when_deleted"));
			echo $check_read_when_deleted->show(intval($RCI->getprop('read_when_deleted')), array('value' => 1));
		?> Mark Messages As Read When Deleted
	</dd>
	<dt class="propname">Preview Pane</dt>
	<div class="description"><strong>Description</strong>: This option chooses whether a preview pane is displayed for viewing messages.</div>
	<dd>
		<?php
			$check_prevpane = new html_checkbox(array('name' => '_preview_pane', 'id' => "cfgprevpane", 'value' => 1));
			echo $check_prevpane->show(intval($RCI->getprop('preview_pane')));
		?>Enable Preview Pane
	</dd>
	<dt class="propname">Focus Window When New Message Arrives</dt>
	<div class="description"><strong>Description</strong>: This bring the browser window/tab to your attention when a new message arrives.</div>
	<dd>
		<?php
			$check_focus_on_new_message = new html_checkbox(array('name' => '_focus_on_new_message', 'id' => "cfgfocus_on_new_message", 'value' => 1));
			echo $check_focus_on_new_message->show(intval($RCI->getprop('focus_on_new_message')));
		?>Focus Window When New Message Arrives
	</dd>
	<dt class="propname">Default Sort Column</dt>
	<div class="description"><strong>Description</strong>: Choose the default column that messages should be sorted by.</div>
	<dd>
		<?php
			$select_message_sort_col = new html_select(array('name' => '_message_sort_col', 'id' => "cfgmessage_sort_col"));
			$select_message_sort_col->add('date', 'date');
			$select_message_sort_col->add('subject', 'subject');
			$select_message_sort_col->add('from', 'from');
			$select_message_sort_col->add('to', 'to');
			$select_message_sort_col->add('cc', 'cc');
			$select_message_sort_col->add('attachment', 'attachment');
			$select_message_sort_col->add('size', 'size');
			$select_message_sort_col->add('flag', 'flag');
			echo $select_message_sort_col->show(intval($RCI->getprop('message_sort_col')));
		?>
	</dd>
	<dt class="propname">Default Sort Order</dt>
	<div class="description"><strong>Description</strong>: This is the default sort order for messages.</div>
	<dd>
		<?php
			$select_message_sort_order = new html_select(array('name' => '_message_sort_order', 'id' => "cfgmessage_sort_order"));
			$select_message_sort_order->add('Descending', 'DESC');
			$select_message_sort_order->add('Ascending', 'ASC');
			echo $select_message_sort_order->show(intval($RCI->getprop('message_sort_order')));
		?>
	</dd>
	<dt class="propname">Default Number of Messages Displayed</dt>
	<div class="description"><strong>Description</strong>: This number determines the number of messages displayed per page when viewing message folders.</div>
		<dd>
		<?php
			$input_pagesize = new html_inputfield(array('name' => '_pagesize', 'size' => 6, 'id' => "cfgpagesize"));
			echo $input_pagesize->show($RCI->getprop('pagesize'));
		?>
		
	</dd>
	<div class="hint"><strong>Note:</strong> This setting can be overridden by the user.</div>
	<dt class="propname">Max Number of Messages Displayed</dt>
	<div class="description"><strong>Description</strong>: This number determines the maximum number of messages displayed per page when viewing message folders.</div>
	<dd>
		<?php
			$input_max_pagesize = new html_inputfield(array('name' => '_max_pagesize', 'size' => 6, 'id' => "cfgmax_pagesize"));
			echo $input_max_pagesize->show($RCI->getprop('max_pagesize'));
		?>
	</dd>
	<dt class="propname">HTML Messages</dt>
	<div class="description"><strong>Description</strong>: This will display HTML messages by default.</div>
	
	<dd>
		<?php
			$check_htmlview = new html_checkbox(array('name' => '_prefer_html', 'id' => "cfghtmlview", 'value' => 1));
			echo $check_htmlview->show(intval($RCI->getprop('prefer_html')));
		?>Allow HTML Messages
	</dd>
	<div class="hint"><strong>Note:</strong> This setting can be overridden by the user.</div>
	<dt class="propname">HTML Editor</dt>
	<div class="description"><strong>Description</strong>: This will allow messages to be edited using the HTML editor.</div>
	
	<dd>
		<?php
			$check_htmlcomp = new html_checkbox(array('name' => '_htmleditor', 'id' => "cfghtmlcompose", 'value' => 1));
			echo $check_htmlcomp->show(intval($RCI->getprop('htmleditor')));
		?>Use the HTML Editor
	</dd>
	<div class="hint"><strong>Note:</strong> This setting can be overridden by the user.</div>
	<!-- These entries are not part of the config but are required for the main.inc.php -->
	<input name="language" size="5" id="language" value="" type="hidden" />
	<input name="timezone" size="5" id="timezone" value="auto" type="hidden" />
	<input name="dst_active" size="5" id="dst_active" value="auto" type="hidden" />
</dl>
</fieldset>



<!--
<div class="spacer"></div>

<fieldset>
<legend class="legend">Address Book Configuration</legend>
<dl class="configblock">
	<dt class="propname">What type of Address Books to Use</dt>
	<div class="description"><strong>Description</strong>: You can choose two types of address books currently; SQL and/or LDAP.</div>
	<dd>
		<?php
			//$select_address_book_type = new html_select(array('name' => '_address_book_type', 'id' => "cfgaddress_book_type"));
			//$select_address_book_type->add('SQL', 'sql');
			//$select_address_book_type->add('LDAP', 'ldap');
			//$select_address_book_type->add('SQL & LDAP', 'isql,ldap');
			//echo $select_address_book_type->show(intval($RCI->getprop('address_book_type')));
		?>
	</dd>
	<dt class="propname">Name of LDAP Address Books</dt>
	<div class="description"><strong>Description</strong>: What should this address book be called?</div>
	<dd>
		<?php
			//$input_ldap_name = new html_inputfield(array('name' => '_ldap_name', 'size' => 6, 'id' => "cfgldap_name"));
			//echo $input_ldap_name->show($RCI->getprop('ldap_name'));
		?> Example: My Contacts<br />
	</dd>
	<dt class="propname">LDAP Server</dt>
	<div class="description"><strong>Description</strong>: List the hostname(s) for the LDAP server(s).</div>
	<dd>
		<div id="defaulthostlist">
		<?php
		//	$ldap_hosts = new html_inputfield(array('name' => '_ldap_hosts[]', 'size' => 30));
		//	echo $ldap_hosts->show($RCI->getprop('ldap_hosts'));

		//	if (empty($ldap_hosts))
	  	//		$ldap_hosts = array('');

	  	//		$i = 0;
	  	//		foreach ($ldap_hosts as $host) {
		//    		echo '<div id="ldap_hosts'.$i.'">' . $text_ldaphost->show($host);
		//	  	if ($i++ > 0)
		//		    echo '<a href="#" onclick="removehostfield(this.parentNode);return false" class="removelink" title="Remove this entry">remove</a>';
		//	    	echo '</div>';
	  	//		}
		?>
<!--
		</div>
<!--		
		<div><a href="javascript:addhostfield()" class="addlink" title="Add another field">add</a></div>
	</dd>
	<dt class="propname">LDAP Port</dt>
	<div class="description"><strong>Description</strong>: LDAP port to connect on.</div>
	<dd>
		<?php
			//$input_ldap_port = new html_inputfield(array('name' => '_ldap_port', 'size' => 6, 'id' => "cfgldap_port"));
			//echo $input_ldap_port->show($RCI->getprop('ldap_port'));
		?> 
	</dd>
	<dt class="propname">LDAP Version</dt>
	<div class="description"><strong>Description</strong>: What version of LDAP should be used?</div>
	<dd>
		<select name="ldap_version" id="ldap_version">
			<option value="2" selected="selected">LDAP Version 2</option>
			<option value="3">LDAP Version 3.</option>
		</select>
	</dd>
	<dt class="propname">LDAP StartTLS</dt>
	<div class="description"><strong>Description</strong>: Enable this is your server requires StartTLS authentication.</div>
	<dd>
		<select name="ldap_tls" id="ldap_tls">
			<option value="false" selected="selected">No, do not use StartTLS.</option>
			<option value="true">Yes, use StartTLS.</option>
		</select>
	</dd>
	<dt class="propname">LDAP Search Scope</dt>
	<div class="description"><strong>Description</strong>: Determine what level searches should go to for the LDAP server.</div>
	<dd>
		<select name="ldap_scope" id="ldap_scope">
			<option value="sub" selected="selected">SUB</option>
			<option value="base">BASE</option>
			<option value="list">LIST</option>
		</select>
	</dd>
	<dt class="propname">LDAP Search Type</dt>
	<div class="description"><strong>Description</strong>: Does the LDAP server allow for wildcard (fuzzy) searches?</div>
	<dd>
		<select name="ldap_fuzzy" id="ldap_fuzzy">
			<option value="true" selected="selected">Yes, the LDAP server supports wildcards.</option>
			<option value="false">No, the LDAP server does not support wildcards.</option>
		</select>
	</dd>
	<dt class="propname">Bind As User</dt>
	<div class="hint"><strong>Note:</strong> This will use the users username and password as well as the base DN to authenticate.</div>
	<dd>
		<select name="ldap_userbind" id="ldap_userbind">
			<option value="false" selected="selected">No, do not bind as user.</option>
			<option value="true">Yes, bind as user.</option>
		</select><br /><br />
	</dd>
	<dt class="propname">Base DN</dt>
	<div class="description"><strong>Description</strong>: Base DN of LDAP server.</div>
	<dd>
		<input name="ldap_base" size="50" id="ldap_base" value="" type="text" />Example: ou=people,dc=example,dc=com
	</dd>
	<dt class="propname">Bind DN</dt>
	<div class="description"><strong>Description</strong>: Bind DN of LDAP server.</div>
	<dd>
		<input name="ldap_bind" size="50" id="ldap_bind" value="" type="text" />Example: cn=search,dc=example,dc=com
	</dd>
	<dt class="propname">Bind Password</dt>
	<div class="description"><strong>Description</strong>: Bind Password of LDAP server.</div>
	<dd>
		<input name="ldap_password" size="50" id="ldap_password" value="" type="password" />
	</dd>
	<dt class="propname">Address Book Writeable?</dt>
	<div class="description"><strong>Description</strong>: Do users have write access to the address book?</div>
	<dd>
		<select name=ldap_writeable" id="ldap_writeable">
				<option value="false" selected="selected">No, LDAP is not writeable.</option>
				<option value="true">Yes, LDAP is writeable.</option>
		</select><br /><br />
	</dd>
	<dt class="propname">LDAP Object Classes</dt>
	<div class="description"><strong>Description</strong>: Provide a comma seperated list of LDAP object classes to be used when creating a new address book entry.</div>
	<dd>
		<input name="ldap_objectclass" size="50" id="ldap_objectclass" value="" type="text" /> Example: top, inetOrgPerson
	</dd>
	<dt class="propname">LDAP Required Fields</dt>
	<div class="description"><strong>Description</strong>: Provide a comma seperated list of LDAP required fields from the object classes above when creating a new address book entry.</div>
	<div class="hint"><strong>Note:</strong> This can also include additional fields not required by the object classes.</div>
	<dd>
		<input name="ldap_required" size="50" id="ldap_required" value="" type="text" /> Example: cn, sn, mail
	</dd>
	<dt class="propname">LDAP Search Fields</dt>
	<div class="description"><strong>Description</strong>: Provide a comma seperated list of LDAP search fields for searching the address book.</div>
	<dd>
		<input name="ldap_search" size="50" id="ldap_search" value="" type="text" /> Example: cn, mail
	</dd>
	<dt class="propname">LDAP RDN</dt>
	<div class="description"><strong>Description</strong>: This is the RDN that is used for new entries. This field must be one of the searchable fields.</div>
	<div class="hint"><strong>Note:</strong> The base DN is appended to the RDN to insert into the address book.</div>
	<dd>
		<input name="ldap_rdn" size="50" id="ldap_rdn" value="" type="text" /> Example: mail
	</dd>
	<dt class="propname">LDAP New Entry: First Name</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used to store the First Name of address book entries.</div>
	<dd>
		<input name="ldap_fname" size="50" id="ldap_fname" value="" type="text" /> Example: gn
	</dd>	
	<dt class="propname">LDAP New Entry: Last Name</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used to store the Last Name of address book entries.</div>
	<dd>
		<input name="ldap_lname" size="50" id="ldap_lname" value="" type="text" /> Example: sn
	</dd>	
	<dt class="propname">LDAP New Entry: Full Name</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used to store the Full Name of address book entries.</div>
	<dd>
		<input name="ldap_fullname" size="50" id="ldap_fullname" value="" type="text" /> Example: cn
	</dd>	
	<dt class="propname">LDAP New Entry: Email Address</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used to store the Email Address of address book entries.</div>
	<dd>
		<input name="ldap_email" size="50" id="ldap_email" value="" type="text" /> Example: mail
	</dd>	
	<dt class="propname">LDAP Entry Sort</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used sort address book entries.</div>
	<dd>
		<input name="ldap_sort" size="50" id="ldap_sort" value="" type="text" /> Example: cn
	</dd>	
	<dt class="propname">LDAP Filter</dt>
	<div class="description"><strong>Description</strong>: This is the attribute used to filter out address book entries from display.</div>
	<dd>
		<input name="ldap_filter" size="50" id="ldap_filter" value="" type="text" /> Example: accountStatus=active
	</dd>	
	-->
	<!-- These entries are not part of the config but are required for the main.inc.php -->
	<input name="autocomplete_addressbooks" size="5" id="autocomplete_addressbooks" value="sql" type="hidden" />
<!--
</dl>
</fieldset>

-->
<?php if ($_POST['level'] == 2) {$_POST['level'] = "2";} else if ($_POST['level'] == 1) {echo '</div>';$_POST['level'] = "1";} ?>
<?php if ($_GET['level'] == 2) {$_POST['level'] = "2";} else if ($_GET['level'] == 1) {echo '</div>';$_POST['level'] = "1";} ?>
<?php
$_GET['level'] = "";

echo '<p><div id="button"><input type="submit" name="submit" value="' . ($RCI->configured ? 'Update' : 'Create') . ' Configuration Files" ' . ($RCI->failures ? 'disabled' : '') . ' /></div></p><br>';

?>
</form>
</div>
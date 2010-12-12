<?php
/*
#########################################################
#             Crystal Webmail Admin Script              #
#                   By: Hunter Dolan                    #
#  You may not remove/modify this message or the above  #
#          without written permission from the          #
#                       author.                         #
#########################################################
# Goal:	Create a script that updates in the background  #
#	without a performance drop and only runs twice a    #
#					       day.	                        #
#            APPROVED BY CRYSTAL TEAM ADMIN             #
#########################################################
*/

include ('auth.php');
if ($_GET['hmmm'] == "yep") {
echo '<br><br><br><br><br><center><div id="intro_text"><h1>HMMMMM... It doesn\'t look like you have any admin panel plugins installed!</h1><br><h3>Don\'t worry it just means that you haven\'t installed any plugins that utilize the Crystal Mail Admin Panel\'s Plugin API.</h3></div></center>';
die();
}
include ('../config/main.inc.php');

if (file_exists('../skins/'.$cmail_config['skin'].'/admin/template.php')) {
$template = '../skins/'.$cmail_config['skin'].'/admin/template.php';
}
else {
$template = '../skins/crystal/admin/template.php';
}

function finish_it() {
if ($_GET['page'] == 'plugins') {
echo "'s plugins section!";
}
else if ($_GET['page'] == 'dashboard' or $_GET['page'] == '') {
echo "!";
}
}
function name() {
$user = strstr($_SESSION['user'], '@', true);
$user = current(explode('@', $_SESSION['user']));  
echo '<u>'.$user.'</u>';
}

function version() {
$ov = 'true';
include ('../program/include/iniset.php');
echo cmail_VERSION;

}

function tabs() {
echo '<span id="dashboard-tab" class="tablink';if ($_GET['page'] == 'dashboard') {echo "-active";}else if ($_GET['page'] == ''){echo "-active";}echo '"><a href="?page=dashboard">Dashboard</a></span>';
echo '<span id="plugins-tab" class="tablink';if ($_GET['page'] == 'plugins') {echo "-active";}echo'"><a href="?page=plugins">Plugins</a></span>';
}

function dashboard_nav() {
echo '
<div id="check">
<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\'updater.php\',\'prefs-box\');">Updates</a></tr></td>
<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\'user-man.php\',\'prefs-box\');">User Management</a></tr></td>
<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\'main-config-editor.php\',\'prefs-box\');">Main.inc.php editor</a></tr></td>
<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\'db-config-editor.php\',\'prefs-box\');">Db.inc.php editor</a></tr></td>
<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\'meebo.php\',\'prefs-box\');">Meebo Toolbar Config</a></tr></td>
</div>
';
}

function nav(){
if ($_GET['page'] == 'plugins') {
include('../config/main.inc.php');
 foreach(glob('../plugins/*/admin_plugin.php') as $path) {
  
     include($path);
    if (in_array($plugin_name, $cmail_config['plugins']))
    
$content_path = str_replace("admin_plugin.php", "admin_plugin_content.php", $path);
     echo '<tr id="rcmrowmailbox"><td "class="section"><a href="javascript:ajaxpage(\''.$content_path.'\',\'prefs-box\');">'.$title.'</a></tr></td>';
}

}else if ($_GET['page'] == "dashboard") {
dashboard_nav();
} else if ($_GET['page'] == "") {
dashboard_nav();
}
}
include ($template);
echo '
<script>
if (document.getElementById("rcmrowmailbox") == undefined)
{
ajaxpage(\'index.php?hmmm=yep\',\'prefs-box\');
}
</script>';
?>

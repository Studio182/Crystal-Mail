<?php
/*
#########################################################
#             Crystal Webmail Meebo Script              #
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
include ('../config/main.inc.php');

if ($_GET['_action'] == 'save') {
// Some variables to work with
$file_location = '../config/main.inc.php';
$search_for_term = 'cmail_config[\'meebo_code\'] = \''.$cmail_config['meebo_code'].'\';';
$replace_with_term = 'cmail_config[\'meebo_code\'] = \''.$_POST["code"].'\';';

// The function that does the replacing
function search_replace($search, $replace, $subject)
{
	$file_in_string = file_get_contents($subject);
	if(!file_put_contents($subject, str_replace($search, $replace, $file_in_string)))
	{
		return FALSE;
	}
	else
	{
		return TRUE;
	}
}

// Execute the function
search_replace($search_for_term, $replace_with_term, $file_location);
echo "<meta http-equiv=\"REFRESH\" content=\"0;url=meebo.php?show=1&message=success\">";
die();
}

if ($_GET['show'] == '') {
echo '<iframe src="meebo.php?show=1" scrolling="auto" frameborder="no" height = "100%" width = "100%"></iframe>';
die();
}
?>
<center>
 <script src="../program/js/jquery-1.4.min.js"></script>
	
	
	<!--[if lt IE 7]>
	<style> 
	div.apple_overlay {
		background-image:url(http://static.flowplayer.org/tools/img/overlay/overlay_IE6.gif);
		color:#fff;
	}
	
	/* default close button positioned on upper right corner */
	div.apple_overlay div.close {
		background-image:url(http://static.flowplayer.org/tools/img/overlay/overlay_close_IE6.gif);
	
	}	
	</style>
	<![endif]--> 
	<style>
	body {
	font-family:Arial,Helvetica,sans-serif;
	font-size:12px;
	color: #333;
	padding: 10px;
	}
	h2 {font-size:20px;}
	</style>
<?php
if ($_GET["message"] == "success") {
echo "<script type='text/javascript'>                                         
        $(document).ready(function() {
    setTimeout(function() { $('#message').fadeOut(); }, 2000);
    });
</script>
<div id='message' style='overflow: none; position:absolute; top:0; left:0; background-color: #FFF; width: 100%; height:100%; font-size: 30px; color: green;'><br><br><br>Success!</div>
";
}
?>
<h2>Meebo Toolbar Config</h2>
				<form action="meebo.php?_action=save" method="post">
<p>Meebo Bar ID:<input type="text" value="<?php echo$cmail_config['meebo_code']?>" name="code";> <font size="1" color="#333" face="Verdana">(<a href="#" id="show-menu">What?</a>)</font><br>
<br>
<input type="submit" />
</form>
</p>
<div id="what"style="display: none;">
<p>
<h4>What is Meebo?</h4>
<div id="what-is-meebo">
The Meebo Bar allows users to connect to multiple IM networks including, Facebook, Google Talk, Twitter, Myspace Chat, AIM, Yahoo (Requires a meebo account), and ICQ (Requires a meebo account). Click <a href="http://bar.meebo.com" target="_blank">Here</a> for a demo!</p>
</div>

<h4>How Do I get a Meebo Bar?</h4>
<div id="get">
<strong>Step 1:</strong>Sign Up for a Meebo Bar account. To get started click <a href="https://bar.meebo.com/setup/1/" target="_blank">Here</a>.<br><br>
<strong>Step 2:</strong>After you have Entered your information and click Continue you will be asked for your <strong>Site Name</strong> and <strong>Site URL</strong> (It is very important that you enter this informaion correctly, If your Crystal Mail installation is on a sub-domain you will need to enter the sub-domain as well).<br><br>
<strong>Step 3:</strong>No you will be directed to a page that tells you how to add the meebo bar to your site. Crystal Mail is pre-configured for Meebo support all you will need to do is add your <strong>Meebo Network ID</strong> into the field above, please note <strong>this is not your username</strong>. To find your <strong>Meebo Network ID</strong> look at the url it should say "https://dashboard.meebo.com/<strong>Your Meebo Network ID</strong>/integrate/?fb=true. Copy and Paste your Meebo Network ID into the field above.<br><br>
<strong>Step 4:</strong>Enjoy!
</div>
<div>
</div>
<script>
    $("#show-menu").click(function () {
    $("#what").show("slow");
    });
    
    </script>
</center>
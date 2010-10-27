<?php
/*
#########################################################
#             Crystal Webmail Update Script             #
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


include('auth.php');
if ($_GET['show'] == '') {
echo '<iframe src="updater.php?show=1" scrolling="no" frameborder="no" height = "100%" width = "100%"></iframe>';
die();
}
  function cm_download($file, $name)
    {
      $contents = file_get_contents($file);
      $name2 = fopen($name, 'w');
      fwrite($name2, $contents);
      fclose($name2);
    }
  function cm_clean($file)
    {
      if (is_array($file))
        {
          foreach ($file as $files)
            {
              unlink($files);
            }
        }
      unlink($file);
    }
?>
  	<script src="../program/js/jquery-1.4.min.js"></script>
  	<script type="text/javascript">
  	                                      
    $(document).ready(function() {
    $('#fade').fadeIn("slow");
});              
</script>
<script>
function Update() {
$('#message').fadeOut('fast');
$('#hidden').prepend('<iframe src="update.php" width="1px" height="1px" onload="finished()">');
$('#updating').fadeIn('slow');
setTimeout(function() { $('#slower').fadeIn(); }, 10000);
}
function finished() {
$('#slower').fadeOut('fast');
$('#updating').fadeOut('fast');
$('#done').fadeIn('slow');
}
</script>
	 <!--[if IE]><script language="javascript" type="text/javascript" src="excanvas.pack.js"></script><![endif]-->
</head>
<body>
<div id="fade" style="display:none;">
<center><img src="ajax-loader.gif"></center>
<center><div style="font-family:arial; font-size:19px; color: #333;">Checking For Updates</div>
</div>
<?php
//Tell iniset.php that you just wan't the version number
$ov = 'true';
include ('../program/include/iniset.php');
 $version = cmail_VERSION; 
cm_download('http://www.crystalmail.net/update/v2/info.php?v='.$version, '../temp/info.php');
  
  //Check if we are in kill mode
  include('../temp/info.php');      
 
  cm_clean('../temp/info.php');
  if ($kill == false)
    {
           if ($version >= $infoversion)
        {
echo "
<script type='text/javascript'>                                         
    $(document).ready(function() {
    $('#fade').fadeOut('slow');
    setTimeout(function() { $('#message').fadeIn(); }, 1500);
});
 </script>  
<div id='message' style='display:none;'>
<center><h1 style='font-family:arial; font-size:30px; color: #333;'>Up to date</h1>
<p style='font-family:arial; font-size:15px; color: #333;'>Your Version of Crystal Mail is the newest possable version. No further action is needed.</p> 
</div>

";
}
else {

echo "<script type='text/javascript'>                                         
    $(document).ready(function() {
    $('#fade').fadeOut('slow');
    setTimeout(function() { $('#message').fadeIn(); }, 1500);
});
 </script>  
 <div id='hidden' style='display:none;'></div>
<div id='message' style='display:none;'>
<center><h1 style='font-family:arial; font-size:30px; color: #333;'><strong>Update Available</strong></h1>
<p style='font-family:arial; font-size:15px; color: #333;'>Crystal Mail is not the newest version possable! Please press the <strong>Update</strong> button below to take advantage of this new update.</p> 
";
if ($release_comment == 'none') {} else { echo "<br><h2 style='font-family:arial; font-size:15px; color: #333;'>Whats new in this version?</h2><p style='font-family:arial; font-size:15px; color: #333;'>".$release_comment."</div>";}
echo "
<input type='button' onclick='Update();' value='Update'/>
</div>
<div id='updating' style='display:none;'>
<center><img src='ajax-loader.gif'></center>
<center><div style='font-family:arial; font-size:19px; color: #333;'>Updating</div><br><div id='slower' style='font-family:arial; color: #333; display:none;'><small>This is taking a bit longer than ususal, but don't worry we can't detect any errors.</div></small></center>
</div>
<div id='done' style='display:none;'>
<center><h1 style='font-family:arial; font-size:30px; color: #333;'>Update Complete!</h1>
<p style='font-family:arial; font-size:15px; color: #333;'>Crystal Mail has finished updating. Click the button below to check if there are any more updates for your system.</p> 
<input type='button' onclick='window.location.reload()' value='Check Again'/>
</center>
</div>
</div>";
}
} else {
echo "
<script type='text/javascript'>                                         
    $(document).ready(function() {
    $('#fade').fadeOut('slow');
    setTimeout(function() { $('#message').fadeIn(); }, 1500);
});
 </script>  
<div id='message' style='display:none;'>
<center><h1 style='font-family:arial; font-size:30px; color: #333;'>Kill mode is activated</h1>
<p style='font-family:arial; font-size:15px; color: #333;'>The Crystal Mail Update System is in <b>Kill Mode</b> this means that the system is shutdown.<br><br>";
if ($kill_message == "") {echo "This is usually due to maintenance. Please check back later";} else { echo $kill_message; } echo "</p> 
</div>
";
}
?>
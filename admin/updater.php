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
</script><script type="text/javascript">
function getXMLHttp()
{
  var xmlHttp

  try
  {
    //Firefox, Opera 8.0+, Safari
    xmlHttp = new XMLHttpRequest();
  }
  catch(e)
  {
    //Internet Explorer
    try
    {
      xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch(e)
    {
      try
      {
        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch(e)
      {
        alert("Your browser does not support AJAX!")
        return false;
      }
    }
  }
  return xmlHttp;
}
function Update()
{
   $('#message').fadeOut('slow');
    setTimeout(function() { $('#updating').fadeIn(); }, 1000);
  var xmlHttp = getXMLHttp();
  
  xmlHttp.onreadystatechange = function()
  {
    if(xmlHttp.readyState == 4)
    {
      HandleResponse(xmlHttp.responseText);
    }
  }

  xmlHttp.open("GET", "update.php", true); 
  xmlHttp.send(null);
  setTimeout(function() { 
    $('#updating').fadeOut('slow');
    setTimeout(function() { $('#done').fadeIn(); }, 1500); 
    }, 4000);
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

cm_download('http://www.crystalmail.net/update/v2/info.php?v='.cmail_VERSION, '../temp/info.php');
  
  //Check if we are in kill mode
  include('../temp/info.php');      
  cm_clean('../temp/info.php');
  if ($kill == false)
    {
     //See if Update Exists
      
      if ($version < $infoversion)
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
<div id='message' style='display:none;'>
<center><h1 style='font-family:arial; font-size:30px; color: #333;'><strong>Update Available</strong></h1>
<p style='font-family:arial; font-size:15px; color: #333;'>Crystal Mail is not the newest version possable! Please press the <strong>Update</strong> button below to take advantage of this new update.</p> 
<input type='button' onclick='Update();' value='Update'/>
</div>
<div id='updating' style='display:none;'>
<center><img src='ajax-loader.gif'></center>
<center><div style='font-family:arial; font-size:19px; color: #333;'>Updating</div>
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
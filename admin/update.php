<?php
/*
#########################################################
#          Crystal Webmail Update Check Script          #
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

//Tell iniset.php that you just wan't the version number (be polite)
$_GET['what_do_you_want'] = 'just_the_version_number_please';
include ('../program/include/iniset.php');

// Download Function
function cm_download($url, $file){
$download = file_get_contents($url, true);
$formatted = $download."\n";
$fh = fopen($file, 'w') or die("can't open file");
fwrite($fh, $formatted);
fclose($fh);
}

cm_download ('http://www.crystalmail.net/update/dev/info.php?u='.cmail_VERSION, 'info.php');


//Include info.php
include('info.php');

//Download it!
cm_download ($url, 'latest.zip');
//Unzip Update
  include('../program/crystal/update/pclzip.lib.php');
  $archive = new PclZip('latest.zip');
  if ($archive->extract(PCLZIP_OPT_PATH, '../',PCLZIP_OPT_REPLACE_NEWER ) == 0) {
    die("Error : ".$archive->errorInfo(true));
  }
 
 //Run install script if there is one
if (file_exists('../install.php')) {
include ('../install.php');
} else {}

//Delete the update's zip file
unlink ('latest.zip');

//Delete the install script if there is one
if (file_exists('../install.php')) {
unlink ('../install.php');
} else {}

unlink ('info.php');
?>

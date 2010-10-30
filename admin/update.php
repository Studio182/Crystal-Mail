<?php
include('auth.php');
if ($_GET['debug'] == "1") {}else{error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);}
  $ov = "true";
  require_once "../program/include/iniset.php";
  $version = cmail_VERSION; 

  function cm_unzip($file, $location)
    {
      require_once "../program/crystal/update/dUnzip2.inc.php";
      require_once "../program/crystal/update/dZip.inc.php";
      $zip = new dUnzip2($file);
      $zip->debug = false;
      $zip->getList();
      $zip->unzipAll($location);
      return "
Successfully Extracted Files";
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
  function mover($src, $dst)
    {
      // Opens source dir.
      $handle = opendir($src);
      // Make dest dir.
      if (!is_dir($dst))
          mkdir($dst, 0755);
      while ($file = readdir($handle))
        {
          if (($file != ".") and ($file != ".."))
            {
              // Skips . and .. dirs
              $srcm = $src . "/" . $file;
              $dstm = $dst . "/" . $file;
              if (is_dir($srcm))
                {
                  // If another dir is found
                  // calls itself - recursive WTG
                  mover($srcm, $dstm);
                }
              else
                {
                  copy($srcm, $dstm);
                  // Is just a copy procedure is needed
                  unlink($srcm);
                }
              // comment out this line
            }
        }
      closedir($handle);
      // and this one also :)
      rmdir($src);
    }
  //Download Info File
  
  cm_download('http://www.crystalmail.net/update/v2/info.php?v=' . $version, '../temp/info.php');
  
  //Check if we are in kill mode
  
  if ($kill == false)
    {
      //Include Info File
      
      include('../temp/info.php');
      
      //Clean up Info File
      
      cm_clean('../temp/info.php');
      
      //See if Update Exists
      
      if ($version < $infoversion)
        {
          //If So Download it
          
          cm_download($url, '../temp/update.zip');
          
          //Extract it
          
          cm_unzip('../temp/update.zip', '../temp/');
          
          //Move into place
          mover('../temp/' . $githubusername . '-' . $githubrepo . '-' . $hash . '/', '../');
          
          //Run Update Script (If any)
          if (file_exists('../install-update.php')) {
          include('../install-update.php');
          }
          
          //And clean your room
          
          cm_clean('../temp/update.zip');
        }
    }
?>
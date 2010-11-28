<?php

/**
 * Show some love for Crystal Mail and tell the world what you are using!
 */
class mobile extends crystal_plugin
{
  public $task = 'settings';

  function init()
  {
    $this->add_texts('localization/', array('mobile'));
    $this->register_action('plugin.mobile', array($this, 'mobilestep'));
    $this->include_script('mobile.js');
  }

  function mobilestep()
  {
    $this->register_handler('plugin.body', array($this, 'mobilehtml'));
    cmail::get_instance()->output->send('plugin');
  }
  
  function mobilehtml()
  {
   $cmail = cmail::get_instance(); 
   
  	$out = "
  	<br><br><br><br><center>
  	<h1>Crystal Mail Mobile</h1>
  	<p><h3>Link App to Crystal Mail</h3><br>
  	<img src='api/index.php?qr_code&user=".$cmail->user->data['username']."'><br>
  	<br>
  	Scan the following QR Code with the Crystal Mail App by pressing the \"+\" button at the top of the main app screen and the click \"Scan QR Code\".
  	</center>";
  	
    return $out;
  }

}

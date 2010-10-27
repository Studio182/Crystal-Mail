<?php

/**
 * Show some love for Crystal Mail and tell the world what you are using!
 */
class about extends crystal_plugin
{
  public $task = 'settings';

  function init()
  {
    $this->add_texts('localization/', array('about'));
    $this->register_action('plugin.about', array($this, 'aboutstep'));
    $this->include_script('about.js');
  }

  function aboutstep()
  {
    $this->register_handler('plugin.body', array($this, 'abouthtml'));
    cmail::get_instance()->output->send('plugin');
  }
  
  function abouthtml()
  {
    $abouturlformed = 'http://www.crystalmail.net/about/about_page.php?version='.cmail_VERSION;
    $out = file_get_contents($abouturlformed);
    return $out;
  }

}

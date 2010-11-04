<?php

/**
 * Show additional message headers
 *
 * Proof-of-concept plugin which will fetch additional headers
 * and display them in the message view.
 *
 * Enable the plugin in config/main.inc.php and add your desired headers:
 *   $cmail_config['show_additional_headers'] = array('User-Agent');
 *
 * @version 1.0
 * @author Thomas Bruederli
 * @website http://crystalmail.net
 */
class show_additional_headers extends crystal_plugin
{
  public $task = 'mail';
  
  function init()
  {
    $cmail = cmail::get_instance();
    if ($cmail->action == 'show' || $cmail->action == 'preview') {
      $this->add_hook('imap_init', array($this, 'imap_init'));
      $this->add_hook('message_headers_output', array($this, 'message_headers'));
    } else if ($cmail->action == '') {
      // with enabled_caching we're fetching additional headers before show/preview
      $this->add_hook('imap_init', array($this, 'imap_init'));
    }
  }
  
  function imap_init($p)
  {
    $cmail = cmail::get_instance();
    if ($add_headers = (array)$cmail->config->get('show_additional_headers', array()))
      $p['fetch_headers'] = trim($p['fetch_headers'].' ' . strtoupper(join(' ', $add_headers)));

    return $p;
  }

  function message_headers($p)
  {
    $cmail = cmail::get_instance();
    foreach ((array)$cmail->config->get('show_additional_headers', array()) as $header) {
      $key = strtolower($header);
      if ($value = $p['headers']->others[$key])
        $p['output'][$key] = array('title' => $header, 'value' => $value);
    }

    return $p;
  }
}

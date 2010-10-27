<?php

/**
 * Mark as Junk
 *
 * Sample plugin that adds a new button to the mailbox toolbar
 * to mark the selected messages as Junk and move them to the Junk folder
 *
 * @version @package_version@
 * @author Thomas Bruederli
 */
class markasjunk extends crystal_plugin
{
  public $task = 'mail';

  function init()
  {
    $cmail = cmail::get_instance();

    $this->register_action('plugin.markasjunk', array($this, 'request_action'));
      
    if ($cmail->action == '' || $cmail->action == 'show') {
      $skin_path = $this->local_skin_path();
      $this->include_script('markasjunk.js');
      $this->add_texts('localization', true);
      $this->add_button(array(
        'command' => 'plugin.markasjunk',
        'imagepas' => $skin_path.'/junk_pas.png',
        'imageact' => $skin_path.'/junk_act.png',
	'title' => 'markasjunk.buttontitle'), 'toolbar');
    }
  }

  function request_action()
  {
    $this->add_texts('localization');

    $GLOBALS['IMAP_FLAGS']['JUNK'] = 'Junk';
    $GLOBALS['IMAP_FLAGS']['NONJUNK'] = 'NonJunk';
    
    $uids = get_input_value('_uid', crystal_INPUT_POST);
    $mbox = get_input_value('_mbox', crystal_INPUT_POST);
    
    $cmail = cmail::get_instance();
    $cmail->imap->unset_flag($uids, 'NONJUNK');
    $cmail->imap->set_flag($uids, 'JUNK');
    
    if (($junk_mbox = $cmail->config->get('junk_mbox')) && $mbox != $junk_mbox) {
      $cmail->output->command('move_messages', $junk_mbox);
    }
    
    $cmail->output->command('display_message', $this->gettext('reportedasjunk'), 'confirmation');
    $cmail->output->send();
  }

}

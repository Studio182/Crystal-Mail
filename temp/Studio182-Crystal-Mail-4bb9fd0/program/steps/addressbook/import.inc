<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/import.inc                                  |
 |                                                                       |
 | This file is part of the Crystal Mail client                     |
 | Copyright (C) 2008-2009, crystalmail Dev. - Switzerland                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Import contacts from a vCard or CSV file                            |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id: $

*/

/**
 * Handler function to display the import/upload form
 */
function cmail_import_form($attrib)
{
  global $cmail, $OUTPUT;
  $target = get_input_value('_target', crystal_INPUT_GPC);
  
  $attrib += array('id' => "rcmImportForm");
  
  $abook = new html_hiddenfield(array('name' => '_target', 'value' => $target));
  $form = $abook->show();

  $upload = new html_inputfield(array('type' => 'file', 'name' => '_file', 'id' => 'rcmimportfile', 'size' => 40));
  $form .= html::p(null, html::label('rcmimportfile', crystal_label('importfromfile')) . html::br() . $upload->show());
  
  $check_replace = new html_checkbox(array('name' => '_replace', 'value' => 1, 'id' => 'rcmimportreplace'));
  $form .= html::p(null, $check_replace->show(get_input_value('_replace', crystal_INPUT_GPC)) .
    html::label('rcmimportreplace', crystal_label('importreplace')));
  
  $OUTPUT->add_label('selectimportfile','importwait');
  $OUTPUT->add_gui_object('importform', $attrib['id']);
  
  $out = html::p(null, Q(crystal_label('importtext'), 'show'));
  
  $out .= $OUTPUT->form_tag(array(
      'action' => $cmail->url('import'),
      'method' => 'post',
      'enctype' => 'multipart/form-data') + $attrib,
    $form);
  
  return $out;
}


/**
 * Render the confirmation page for the import process
 */
function cmail_import_confirm($attrib)
{
  global $IMPORT_STATS;
  
  $vars = get_object_vars($IMPORT_STATS);
  $vars['names'] = join(', ', array_map('Q', $IMPORT_STATS->names));
  
  return html::p($attrib, Q(crystal_label(array(
    'name' => 'importconfirm',
    'nr' => $IMORT_STATS->inserted,
    'vars' => $vars,
  )), 'show'));
}


/**
 * Create navigation buttons for the current import step
 */
function cmail_import_buttons($attrib)
{
  global $IMPORT_STATS, $OUTPUT;
  $target = get_input_value('_target', crystal_INPUT_GPC);
  
  $attrib += array('type' => 'input');
  unset($attrib['name']);
  
  if (is_object($IMPORT_STATS)) {
    $attrib['class'] = trim($attrib['class'] . ' mainaction');
    $out = $OUTPUT->button(array('command' => 'list', 'prop' => $target, 'label' => 'done') + $attrib);
  }
  else {
    $out = $OUTPUT->button(array('command' => 'list', 'label' => 'cancel') + $attrib);
    $out .= '&nbsp;';
    $attrib['class'] = trim($attrib['class'] . ' mainaction');
    $out .= $OUTPUT->button(array('command' => 'import', 'label' => 'import') + $attrib);
  }
  
  return $out;
}


/** The import process **/

$importstep = 'cmail_import_form';

if ($_FILES['_file']['tmp_name'] && is_uploaded_file($_FILES['_file']['tmp_name'])) {
  $replace = (bool)get_input_value('_replace', crystal_INPUT_GPC);
  $target = get_input_value('_target', crystal_INPUT_GPC);
  $CONTACTS = $cmail->get_address_book($target, true);

  // let crystal_vcard do the hard work :-)
  $vcards = crystal_vcard::import(file_get_contents($_FILES['_file']['tmp_name']));

  // no vcards detected
  if (!count($vcards)) {
    $OUTPUT->show_message('importerror', 'error');
  }
  else if ($CONTACTS->readonly) {
    $OUTPUT->show_message('addresswriterror', 'error');
  }
  else {
    $IMPORT_STATS = new stdClass;
    $IMPORT_STATS->names = array();
    $IMPORT_STATS->count = count($vcards);
    $IMPORT_STATS->inserted = $IMPORT_STATS->skipped = $IMPORT_STATS->nomail = $IMPORT_STATS->errors = 0;
    
    if ($replace)
      $CONTACTS->delete_all();
    
    foreach ($vcards as $vcard) {
      $email = $vcard->email[0];
      
      // skip entries without an e-mail address
      if (empty($email)) {
        $IMPORT_STATS->nomail++;
        continue;
      }
      
      if (!$replace) {
        // compare e-mail address
        $existing = $CONTACTS->search('email', $email, false, false);
        if (!$existing->count) {  // compare display name
          $existing = $CONTACTS->search('name', $vcard->displayname, false, false);
        }
        if ($existing->count) {
          $IMPORT_STATS->skipped++;
          continue;
        }
      }
      
      $a_record = array(
        'name' => $vcard->displayname,
        'firstname' => $vcard->firstname,
        'surname' => $vcard->surname,
        'email' => $email,
        'vcard' => $vcard->export(),
      );
      
      $plugin = $cmail->plugins->exec_hook('contact_create', array('record' => $a_record, 'source' => null));
      $a_record = $plugin['record'];

      // insert record and send response
      if (!$plugin['abort'] && ($success = $CONTACTS->insert($a_record))) {
        $IMPORT_STATS->inserted++;
        $IMPORT_STATS->names[] = $vcard->displayname;
      } else {
        $IMPORT_STATS->errors++;
      }
    }

    $importstep = 'cmail_import_confirm';
  }
}
else if ($err = $_FILES['_file']['error']) {
  if ($err == UPLOAD_ERR_INI_SIZE || $err == UPLOAD_ERR_FORM_SIZE) {
    $OUTPUT->show_message('filesizeerror', 'error', array('size' => show_bytes(parse_bytes(ini_get('upload_max_filesize')))));
  } else {
    $OUTPUT->show_message('fileuploaderror', 'error');
  }
}


$OUTPUT->set_pagetitle(crystal_label('importcontacts'));

$OUTPUT->add_handlers(array(
  'importstep' => $importstep,
  'importnav' => 'cmail_import_buttons',
));

// render page
$OUTPUT->send('importcontacts');

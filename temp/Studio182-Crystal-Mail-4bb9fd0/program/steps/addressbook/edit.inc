<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/addressbook/edit.inc                                    |
 |                                                                       |
 | This file is part of the Crystal Mail client                     |
 | Copyright (C) 2005-2007, crystalmail Dev. - Switzerland                 |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Show edit form for a contact entry or to add a new one              |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <roundcube@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id$

*/


if (($cid = get_input_value('_cid', crystal_INPUT_GPC)) && ($record = $CONTACTS->get_record($cid, true)))
  $OUTPUT->set_env('cid', $record['ID']);

// adding not allowed here
if ($CONTACTS->readonly)
{
  $OUTPUT->show_message('sourceisreadonly');
  cmail_overwrite_action('show');
  return;
}

function cmail_contact_editform($attrib)
{
  global $cmail, $CONTACTS, $OUTPUT;

  // check if we have a valid result
  if ($cmail->action != 'add' && !(($result = $CONTACTS->get_result()) && ($record = $result->first())))
  {
    $OUTPUT->show_message('contactnotfound');
    return false;
  }
  $vcard = new crystal_vcard($record['vcard']);

  // add some labels to client
  $OUTPUT->add_label('noemailwarning', 'nonamewarning');

  list($form_start, $form_end) = get_form_tags($attrib);
  unset($attrib['form']);

  // a specific part is requested
  if ($attrib['part'])
  {
    $out = $form_start;
    $out .= disp_contact_field($record, $attrib['part'], $vcard, $attrib);
    return $out;
  }

  // return the complete address edit form as table
  $out = $form_start;

  $curtab = get_input_value('_tab', crystal_INPUT_GPC);
  $curtab = (empty($curtab)) ? 'basic' : $curtab;
  $OUTPUT->set_env('contacttab', $curtab);

  $panes = cmail_contact_fields();
  foreach ($panes as $pane => $a_show_cols) {
    $table = new html_table(array('cols' => 3));

    // add template for email row
    if ($pane == "basic" && array_key_exists('vcard', $CONTACTS->fieldmap)) {
	  $defaults = $attrib;
	  $defaults['id'] = 'rcmfd_altemail_X';
	  $table->set_row_attribs(array('style' => 'display: none;'));
      $table->add(array('class' => 'title', 'style' => 'width: 150px; vertical-align: top;'), html::label("_X", Q(str_replace("%n", "_X", crystal_label('altemail')))));
      $table->add(null, cmail_get_edit_field('altemail[]', '', $defaults));
      $table->add(null, '');
    }

  foreach ($a_show_cols as $col)
  {
    $attrib['id'] = 'rcmfd_'.$col;
      $value = disp_contact_field($record, $col, $vcard, $attrib);

      if (!is_array($value))
        $value = array($value);

      foreach ($value as $idx => $val) {
        if ($col == 'altemail')
          $table->add(array('class' => 'title', 'style' => 'width: 150px; vertical-align: top;'), html::label($attrib['id'] . $idx, Q(str_replace("%n", $idx + 1, crystal_label($col)))));
        else
          $table->add(array('class' => 'title', 'style' => 'width: 150px; vertical-align: top;'), html::label($attrib['id'], Q(crystal_label($col))));

        $table->add(null, $val);

        // "add email address" button
        if ($col == "email" && array_key_exists('vcard', $CONTACTS->fieldmap))
	      $table->add(array('style' => 'padding-left: 5px'), $OUTPUT->button(array('command' => 'add-altemail', 'type' => 'image', 'image' => $attrib['addicon'], 'alt' => 'addaltemail', 'title' => 'addaltemail')));
	    else
          $table->add(null, '');
      }
  }

    $attrib['id'] = 'rcm_'.$pane.'tbl';
    $style = ($pane == $curtab) ? '' : 'display: none;';
    $out .= html::tag('div', array('id' => 'addressdiv' . $pane, 'style' => $style), $table->show($attrib + array('class' => 'vcard')));
  }

  $out .= $form_end;

  return $out;  
}

$OUTPUT->add_handler('contacteditform', 'cmail_contact_editform');

function disp_contact_field($record, $col, $vcard, $attrib) {
  global $OUTPUT;

  if ($col == 'email' && (!empty($record[$col]) || !empty($vcard->email[0]))) {
	$data = ($record[$col]) ? $record[$col] : $vcard->email[0];
    $value = cmail_get_edit_field($col, $data, $attrib);
  }
  else if ($col == 'altemail' && (!empty($record[$col]) || !empty($vcard->email[1]))) {
    $data = ($record[$col]) ? array($record[$col]) : $vcard->email;
    $baseid = $attrib['id'];

    for ($i = 1; $i < sizeof($data); $i++) {
	    $attrib['id'] = $baseid . ($i - 1);
	    $value[] = cmail_get_edit_field($col . '[]', $data[$i], $attrib);
    }
    $OUTPUT->set_env('altemailcount', $i);
  }
  else if ($col == 'altemail') {
    $value = cmail_get_edit_field($col . '[]', '', $attrib);
    $OUTPUT->set_env('altemailcount', 2);
  }
  else if ($col == 'homeaddress' || $col == 'orgaddress') {
    $data = ($record[$col]) ? $record[$col] : $vcard->$col;
    if (is_array($data)) unset($data['type']);
    $value = cmail_get_edit_field($col, is_array($data) ? join("\n", $data) : $data, array_merge($attrib, array('rows' => '6')), 'textarea');
  }
  else if ($col == 'birthday') {
	$data = ($record[$col]) ? $record[$col] : $vcard->$col;
    $value = cmail_get_edit_field($col, format_date(strtotime($data), cmail::get_instance()->config->get('date_only', 'd F Y')), $attrib);
  }
  else if ($col == 'notes') {
	$data = ($record[$col]) ? $record[$col] : $vcard->$col;
    $value = cmail_get_edit_field($col,  $data, array_merge($attrib, array('rows' => '6')), 'textarea');
  }
  else if (!empty($record[$col])) {
    $value = cmail_get_edit_field($col, $record[$col], $attrib);
  }
  else if (!empty($vcard->$col)) {
    $value = cmail_get_edit_field($col, $vcard->$col, $attrib);
  }
  else {
    $value = cmail_get_edit_field($col, '', $attrib);
  }

  return $value;
}

// similar function as in /steps/settings/edit_identity.inc
function get_form_tags($attrib)
{
  global $CONTACTS, $EDIT_FORM, $cmail;

  $form_start = $form_end = '';
  
  if (empty($EDIT_FORM)) {
    $hiddenfields = new html_hiddenfield(array('name' => '_source', 'value' => get_input_value('_source', crystal_INPUT_GPC)));
    $hiddenfields->add(array('id' => 'contacttab', 'name' => '_tab', 'value' => get_input_value('_tab', crystal_INPUT_GPC)));
    $hiddenfields->add(array('name' => '_gid', 'value' => $CONTACTS->group_id));
    
    if (($result = $CONTACTS->get_result()) && ($record = $result->first()))
      $hiddenfields->add(array('name' => '_cid', 'value' => $record['ID']));
    
    $form_start = $cmail->output->request_form(array('name' => "form", 'method' => "post", 'task' => $cmail->task, 'action' => 'save', 'request' => 'save.'.intval($record['ID']), 'noclose' => true) + $attrib, $hiddenfields->show());
    $form_end = !strlen($attrib['form']) ? '</form>' : '';

    $EDIT_FORM = !empty($attrib['form']) ? $attrib['form'] : 'form';
    $cmail->output->add_gui_object('editform', $EDIT_FORM);
  }

  return array($form_start, $form_end); 
}



if (!$CONTACTS->get_result() && $OUTPUT->template_exists('addcontact'))
  $OUTPUT->send('addcontact');

// this will be executed if no template for addcontact exists
$OUTPUT->send('editcontact');


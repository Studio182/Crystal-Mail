<?php
/*
+----------------------------------------------------------------+
| ./program/steps/settings/edit_identity.inc                     |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2007, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Show edit form for a identity record or to add a new one     |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/
 

define('IDENTITIES_LEVEL', intval($cmail->config->get('identities_level', 0)));

// edit-identity
if (($_GET['_iid'] || $_POST['_iid']) && $cmail->action=='edit-identity') {
  $IDENTITY_RECORD = $USER->get_identity(get_input_value('_iid', crystal_INPUT_GPC));
  
  if (is_array($IDENTITY_RECORD))
    $OUTPUT->set_env('iid', $IDENTITY_RECORD['identity_id']);
  else {
    $OUTPUT->show_message('opnotpermitted', 'error');
    // go to identities page
    cmail_overwrite_action('identities');
    return;
  }
}
// add-identity
else {
  if (IDENTITIES_LEVEL > 1) {
    $OUTPUT->show_message('opnotpermitted', 'error');
    // go to identities page
    cmail_overwrite_action('identities');
    return;
  }
  else if (IDENTITIES_LEVEL == 1)
    $IDENTITY_RECORD['email'] = $cmail->user->get_username();
}


function crystal_identity_form($attrib)
  {
  global $IDENTITY_RECORD, $cmail, $OUTPUT;

  crystal_html_editor('identity');

  // add some labels to client
  $OUTPUT->add_label('noemailwarning', 'nonamewarning', 'converting', 'editorwarning');

  $i_size = !empty($attrib['size']) ? $attrib['size'] : 40;
  $t_rows = !empty($attrib['textarearows']) ? $attrib['textarearows'] : 6;
  $t_cols = !empty($attrib['textareacols']) ? $attrib['textareacols'] : 40;

  list($form_start, $form_end) = get_form_tags($attrib, 'save-identity', intval($IDENTITY_RECORD['identity_id']), array('name' => '_iid', 'value' => $IDENTITY_RECORD['identity_id']));
  unset($attrib['form']);

  // list of available cols
  $a_show_cols = array('name'         => array('type' => 'text', 'size' => $i_size),
                       'email'        => array('type' => 'text', 'size' => $i_size),
                       'organization' => array('type' => 'text', 'size' => $i_size),
                       'reply-to'     => array('type' => 'text', 'label' => 'reply-to', 'size' => $i_size),
                       'bcc'          => array('type' => 'text', 'size' => $i_size),
                       'signature'	  => array('type' => 'textarea', 'size' => $t_cols, 'rows' => $t_rows),
                       'html_signature'=>array('type' => 'checkbox', 'label' => 'htmlsignature', 'onclick' => 'return cmail_toggle_editor(this, \'rcmfd_signature\');'),
                       'standard'     => array('type' => 'checkbox', 'label' => 'setdefault'));

  // disable some field according to access level
  if (IDENTITIES_LEVEL == 1 || IDENTITIES_LEVEL == 3) {
    $a_show_cols['email']['disabled'] = true;
    $a_show_cols['email']['class'] = 'disabled';
  }
  
  // a specific part is requested
  if ($attrib['part'])
    {
    $colprop = $a_show_cols[$attrib['part']];
    if (is_array($colprop))
      {
      $out = $form_start;
      $out .= cmail_get_edit_field($attrib['part'], $IDENTITY_RECORD[$attrib['part']], $attrib, $colprop['type']); 
      return $out;
      }
    else
      return '';
    }


  // return the complete edit form as table
  $out = "$form_start<table>\n\n";

  foreach ($a_show_cols as $col => $colprop)
    {
    $colprop['id'] = 'rcmfd_'.$col;

    if ($col == 'signature')
      {
      $colprop['spellcheck'] = true;
      if ($IDENTITY_RECORD['html_signature'])
        {
        $colprop['class'] = 'mce_editor';
        }
      }

    $label = strlen($colprop['label']) ? $colprop['label'] : $col;
    $value = cmail_get_edit_field($col, $IDENTITY_RECORD[$col], $colprop, $colprop['type']);

    $out .= sprintf("<tr><td class=\"title\"><label for=\"%s\">%s</label></td><td>%s</td></tr>\n",
                    $colprop['id'],
                    Q(crystal_label($label)),
                    $value);
    }

  $out .= "\n</table>$form_end";

  return $out;  
  }

$OUTPUT->include_script('list.js');
$OUTPUT->add_handler('identityform', 'crystal_identity_form');
$OUTPUT->set_env('identities_level', IDENTITIES_LEVEL);

$OUTPUT->set_pagetitle(crystal_label(($cmail->action=='add-identity' ? 'newidentity' : 'edititem')));

if ($cmail->action=='add-identity' && $OUTPUT->template_exists('addidentity'))
  $OUTPUT->send('addidentity');

$OUTPUT->send('editidentity');



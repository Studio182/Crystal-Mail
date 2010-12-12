<?php
/*
+----------------------------------------------------------------+
| ./program/steps/settings/edit_prefs.inc                        |
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
|   Provide functionality for user's settings & preferences      |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

if (!$OUTPUT->ajax_call)
  $OUTPUT->set_pagetitle(crystal_label('preferences'));


$CURR_SECTION = get_input_value('_section', crystal_INPUT_GPC);
list($SECTIONS,) = cmail_user_prefs($CURR_SECTION);

function cmail_user_prefs_form($attrib)
{
  global $cmail, $CURR_SECTION, $SECTIONS;

  // add some labels to client
  $cmail->output->add_label('nopagesizewarning');

  unset($attrib['form']);
  
  list($form_start, $form_end) = get_form_tags($attrib, 'save-prefs', null,
    array('name' => '_section', 'value' => $CURR_SECTION));

  $out = $form_start;

  foreach ($SECTIONS[$CURR_SECTION]['blocks'] as $idx => $block) {
    if ($block['options']) {
      $table = new html_table(array('cols' => 2));

      foreach ($block['options'] as $option) {
        if ($option['advanced'])
	  $table->set_row_attribs('advanced');
    
        $table->add('title', $option['title']);
	$table->add(null, $option['content']);
        }
    
      $out .= html::tag('fieldset', null, html::tag('legend', null, $block['name']) . $table->show($attrib));
      }
    }

  return $out . $form_end;
}

function cmail_prefs_section_name()
{
  global $SECTIONS, $CURR_SECTION;
  
  return $SECTIONS[$CURR_SECTION]['section'];
}


// register UI objects
$OUTPUT->add_handlers(array(
  'userprefs' => 'cmail_user_prefs_form',
  'sectionname' => 'cmail_prefs_section_name',
));

$OUTPUT->send('settingsedit');



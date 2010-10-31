<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/groups.inc                         |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2010, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Create/delete/rename contact groups and assign/remove contac |
|   ts                                                           |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

if ($CONTACTS->readonly || !$CONTACTS->groups) {
  $OUTPUT->show_message('sourceisreadonly', 'warning');
  $OUTPUT->send();
}

$source = get_input_value('_source', crystal_INPUT_GPC);

if ($cmail->action == 'group-addmembers') {
  if (($gid = get_input_value('_gid', crystal_INPUT_POST)) && ($ids = get_input_value('_cid', crystal_INPUT_POST))) {
    $plugin = $cmail->plugins->exec_hook('group_addmembers', array('group_id' => $gid, 'ids' => $ids, 'source' => $source));
    
    $CONTACTS->set_group($gid);
    $num2add = count(explode(',', $plugin['ids']));
    
    if (!$plugin['abort'] && ($maxnum = $cmail->config->get('max_group_members', 0)) && ($CONTACTS->count()->count + $num2add > $maxnum))
      $OUTPUT->show_message('maxgroupmembersreached', 'warning', array('max' => $maxnum));
    else if (!$plugin['abort'] && $CONTACTS->add_to_group($gid, $plugin['ids']))
      $OUTPUT->show_message('contactaddedtogroup');
    else if ($plugin['message'])
      $OUTPUT->show_message($plugin['message'], 'warning');
  }
}

else if ($cmail->action == 'group-delmembers') {
  if (($gid = get_input_value('_gid', crystal_INPUT_POST)) && ($ids = get_input_value('_cid', crystal_INPUT_POST))) {
    $plugin = $cmail->plugins->exec_hook('group_delmembers', array('group_id' => $gid, 'ids' => $ids, 'source' => $source));
    
    if (!$plugin['abort'] && $CONTACTS->remove_from_group($gid, $plugin['ids']))
      $OUTPUT->show_message('contactremovedfromgroup');
    else if ($plugin['message'])
      $OUTPUT->show_message($plugin['message'], 'warning');
  }
}

else if ($cmail->action == 'group-create') {
  if ($name = trim(get_input_value('_name', crystal_INPUT_POST))) {
    $plugin = $cmail->plugins->exec_hook('group_create', array('name' => $name, 'source' => $source));
    if (!$plugin['abort'])
      $created = $CONTACTS->create_group($plugin['name']);
  }
  
  if ($created && $OUTPUT->ajax_call) {
    $created['source'] = $source;
    $OUTPUT->command('insert_contact_group', $created);
  }
  else if (!$created) {
    $OUTPUT->show_message($plugin['message'] ? $plugin['message'] : 'errorsaving', 'error');
  }
}

else if ($cmail->action == 'group-rename') {
  if (($gid = get_input_value('_gid', crystal_INPUT_POST)) && ($name = trim(get_input_value('_name', crystal_INPUT_POST)))) {
    $plugin = $cmail->plugins->exec_hook('group_rename', array('group_id' => $gid, 'name' => $name, 'source' => $source));
    if (!$plugin['abort'])
      $newname = $CONTACTS->rename_group($gid, $plugin['name']);
  }

  if ($newname && $OUTPUT->ajax_call)
    $OUTPUT->command('update_contact_group', array('source' => $source, 'id' => $gid, 'name' => $newname));
  else if (!$newname)
    $OUTPUT->show_message($plugin['message'] ? $plugin['message'] : 'errorsaving', 'error');
}

else if ($cmail->action == 'group-delete') {
  if ($gid = get_input_value('_gid', crystal_INPUT_POST)) {
    $plugin = $cmail->plugins->exec_hook('group_delete', array('group_id' => $gid, 'source' => $source));
    if (!$plugin['abort'])
      $deleted = $CONTACTS->delete_group($gid);
  }

  if ($deleted)
    $OUTPUT->command('remove_group_item', array('source' => $source, 'id' => $gid));
  else
    $OUTPUT->show_message($plugin['message'] ? $plugin['message'] : 'errorsaving', 'error');
}

// send response
$OUTPUT->send();


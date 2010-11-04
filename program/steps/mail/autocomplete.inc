<?php

/*
 +-----------------------------------------------------------------------+
 | program/steps/mail/autocomplete.inc                                   |
 |                                                                       |
 | This file is part of the Crystal Mail client                     |
 | Copyright (C) 2008-2010, crystalmail Dev Team                           |
 | Licensed under the GNU GPL                                            |
 |                                                                       |
 | PURPOSE:                                                              |
 |   Perform a search on configured address books for the address        |
 |   autocompletion of the message compose screen                        |
 +-----------------------------------------------------------------------+
 | Author: Thomas Bruederli <crystalmail@gmail.com>                        |
 +-----------------------------------------------------------------------+

 $Id$

*/

$MAXNUM = 15;
$book_types = (array) $cmail->config->get('autocomplete_addressbooks', 'sql');

if ($cmail->action == 'group-expand') {
  $abook = $cmail->get_address_book(get_input_value('_source', crystal_INPUT_GPC));
  if ($gid = get_input_value('_gid', crystal_INPUT_GPC)) {
    $members = array();
    $abook->set_group($gid);
    $abook->set_pagesize(1000);  // TODO: limit number of group members by config
    $result = $abook->list_records(array('email','name'));
    while ($result && ($sql_arr = $result->iterate()))
      $members[] = format_email_recipient($sql_arr['email'], $sql_arr['name']);

    $OUTPUT->command('replace_group_recipients', $gid, join(', ', $members));
  }
}
else if ($book_types && $search = get_input_value('_search', crystal_INPUT_GPC, true)) {
  $contacts = array();
  $books_num = count($book_types);

  foreach ($book_types as $id) {
    $abook = $cmail->get_address_book($id);
    $abook->set_pagesize($MAXNUM);

    if ($result = $abook->auto_complete($search, $MAXNUM, $id)) {
	  $contacts = array_merge($contacts, $result);

      if (count($contacts) >= $MAXNUM) {
        $contacts = array_slice($contacts, 0, $MAXNUM);
            break;
        }
      }
    }

  $contacts = array_unique($contacts);
  usort($contacts, 'contact_results_sort');
}

$OUTPUT->command('ksearch_query_results', $contacts, $search);
$OUTPUT->send();


function contact_results_sort($a, $b)
{
  $name_a = is_array($a) ? $a['name'] : $a;
  $name_b = is_array($b) ? $b['name'] : $b;
  return strcoll(trim($name_a, '" '), trim($name_b, '" '));
}


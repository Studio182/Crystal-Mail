<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/func.inc                           |
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
|   Provide addressbook functionality and GUI objects            |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/

// add list of address sources to client env
$js_list = $cmail->get_address_sources();

// select source
$source = get_input_value('_source', crystal_INPUT_GPC);

// if source is not set use first directory
if (empty($source))
  $source = $js_list[key($js_list)]['id'];

// instantiate a contacts object according to the given source
$CONTACTS = $cmail->get_address_book($source);

$CONTACTS->set_pagesize($CONFIG['pagesize']);

// set list properties and session vars
if (!empty($_GET['_page']))
  $CONTACTS->set_page(($_SESSION['page'] = intval($_GET['_page'])));
else
  $CONTACTS->set_page(isset($_SESSION['page']) ?$_SESSION['page'] : 1);
  
if (!empty($_REQUEST['_gid']))
  $CONTACTS->set_group(get_input_value('_gid', crystal_INPUT_GPC));

// set message set for search result
if (!empty($_REQUEST['_search']) && isset($_SESSION['search'][$_REQUEST['_search']]))
  $CONTACTS->set_search_set($_SESSION['search'][$_REQUEST['_search']]);

// set data source env
$OUTPUT->set_env('source', $source ? $source : '0');
$OUTPUT->set_env('readonly', $CONTACTS->readonly, false);
if(! $OUTPUT->ajax_call) {
  $OUTPUT->set_env('address_sources', $js_list);
  $OUTPUT->set_pagetitle(crystal_label('addressbook'));
}

function cmail_directory_list($attrib)
{
  global $cmail, $OUTPUT;
  
  if (!$attrib['id'])
    $attrib['id'] = 'rcmdirectorylist';

  $out = '';
  $local_id = '0';
  $jsdata = array();
  $current = get_input_value('_source', crystal_INPUT_GPC);
  $line_templ = html::tag('li', array('id' => 'rcmli%s', 'class' => 'addressbook %s'),
    html::a(array('href' => '%s', 'onclick' => "return ".JS_OBJECT_NAME.".command('list','%s',this)"), '%s'));

  if (!$current && strtolower($cmail->config->get('address_book_type', 'sql')) != 'ldap') {
    $current = '0';
  }
  else if (!$current) {
    // DB address book not used, see if a source is set, if not use the
    // first LDAP directory.
    $current = key((array)$cmail->config->get('ldap_public', array()));
  }

  foreach ((array)$OUTPUT->env['address_sources'] as $j => $source) {
    $id = $source['id'] ? $source['id'] : $j;
    $js_id = JQ($id);
    $dom_id = preg_replace('/[^a-z0-9\-_]/i', '', $id);
    $out .= sprintf($line_templ, $dom_id, ($current == strval($id) ? 'selected' : ''),
      Q(cmail_url(null, array('_source' => $id))), $js_id, (!empty($source['name']) ? Q($source['name']) : Q($id)));
    $groupdata = cmail_contact_groups(array('out' => $out, 'jsdata' => $jsdata, 'source' => $id));
    $jsdata = $groupdata['jsdata'];
    $out = $groupdata['out'];
  }

  $OUTPUT->set_env('contactgroups', $jsdata); 
  $OUTPUT->add_gui_object('folderlist', $attrib['id']);
  
  return html::tag('ul', $attrib, $out, html::$common_attrib);
}


function cmail_contact_groups($args)
{
  global $cmail;

  $groups = $cmail->get_address_book($args['source'])->list_groups();

  if (!empty($groups)) {
    $line_templ = html::tag('li', array('id' => 'rcmliG%s%s', 'class' => 'contactgroup'),
      html::a(array('href' => '#', 'onclick' => "return ".JS_OBJECT_NAME.".command('listgroup',{'source':'%s','id':'%s'},this)"), '%s'));

    $jsdata = array();
    foreach ($groups as $group) {
      $args['out'] .= sprintf($line_templ, $args['source'], $group['ID'], $args['source'], $group['ID'], Q($group['name']));
      $args['jsdata']['G'.$args['source'].$group['ID']] = array(
        'source' => $args['source'], 'id' => $group['ID'], 'name' => $group['name'], 'type' => 'group');
    }
  }

  return $args;
}


// return the message list as HTML table
function cmail_contacts_list($attrib)
  {
  global $CONFIG, $CONTACTS, $OUTPUT;
  
  // count contacts for this user
  $result = $CONTACTS->list_records();
  
  // add id to message list table if not specified
  if (!strlen($attrib['id']))
    $attrib['id'] = 'rcmAddressList';
  
  // define list of cols to be displayed
  if (empty($attrib['columns']))
      $a_show_cols = is_array($CONFIG['address_columns']) ? $CONFIG['address_columns'] : array('name');
  else
      $a_show_cols = preg_split('/[\s,;]+/', strip_quotes($attrib['columns']));

  // store column list in a session-variable
  $_SESSION['address_columns'] = $a_show_cols;

  // create XHTML table
  $out = crystal_table_output($attrib, $result->records, $a_show_cols, $CONTACTS->primary_key);
  
  // set client env
  $OUTPUT->add_gui_object('contactslist', $attrib['id']);
  $OUTPUT->set_env('current_page', (int)$CONTACTS->list_page);
  $OUTPUT->set_env('pagecount', ceil($result->count/$CONTACTS->page_size));
  $OUTPUT->include_script('list.js');
  
  // add some labels to client
  $OUTPUT->add_label('deletecontactconfirm');
  
  return $out;
  }


function cmail_js_contacts_list($result, $prefix='')
  {
  global $CONFIG, $OUTPUT;

  if (empty($result) || $result->count == 0)
    return;

  // define list of cols to be displayed
  if (empty($_SESSION['address_columns']))
    $a_show_cols = is_array($CONFIG['address_columns']) ? $CONFIG['address_columns'] : array('name');
  else
    $a_show_cols = $_SESSION['address_columns'];
  
  while ($row = $result->next())
    {
    $a_row_cols = array();
    
    // format each col
    foreach ($a_show_cols as $col)
      $a_row_cols[$col] = Q($row[$col]);
    
    $OUTPUT->command($prefix.'add_contact_row', $row['ID'], $a_row_cols);
    }
  }


// similar function as /steps/settings/identities.inc::cmail_identity_frame()
function cmail_contact_frame($attrib)
  {
  global $OUTPUT;

  if (!$attrib['id'])
    $attrib['id'] = 'rcmcontactframe';
    
  $attrib['name'] = $attrib['id'];

  $OUTPUT->set_env('contentframe', $attrib['name']);
  $OUTPUT->set_env('blankpage', $attrib['src'] ? $OUTPUT->abs_url($attrib['src']) : 'program/blank.gif');

  return html::iframe($attrib);
  }


function cmail_rowcount_display($attrib)
  {
  global $OUTPUT;
  
  if (!$attrib['id'])
    $attrib['id'] = 'rcmcountdisplay';

  $OUTPUT->add_gui_object('countdisplay', $attrib['id']);

  return html::span($attrib, cmail_get_rowcount_text());
  }


function cmail_contact_tabs($attrib)
  {
  global $cmail, $OUTPUT, $CONTACTS;
  $out = "";

  $button = $OUTPUT->button(array("command" => "abooknav", "prop" => "basic", "type" => "link", "label" => "basicinfo", "title" => "basicinfo"));
  $out = html::span(array("id" => "addresstabbasic", "class" => "tablink"), $button);

  $tabs = cmail_contact_fields();

  if (sizeof($tabs['home']) > 0 && $cmail->action != 'blank') {
    $button = $OUTPUT->button(array("command" => "abooknav", "prop" => "home", "type" => "link", "label" => "homecontact", "title" => "homecontact"));
    $out .= html::span(array("id" => "addresstabhome", "class" => "tablink"), $button);
  }

  if (sizeof($tabs['business']) > 0 && $cmail->action != 'blank') {
    $button = $OUTPUT->button(array("command" => "abooknav", "prop" => "business", "type" => "link", "label" => "businesscontact", "title" => "businesscontact"));
    $out .= html::span(array("id" => "addresstabbusiness", "class" => "tablink"), $button);
  }

  if (sizeof($tabs['other']) > 0 && $cmail->action != 'blank') {
    $button = $OUTPUT->button(array("command" => "abooknav", "prop" => "other", "type" => "link", "label" => "otherinfo", "title" => "otherinfo"));
    $out .= html::span(array("id" => "addresstabother", "class" => "tablink"), $button);
  }

  if ($CONTACTS->groups && sizeof($CONTACTS->list_groups()) > 0 && $cmail->action == 'show') {
    $button = $OUTPUT->button(array("command" => "abooknav", "prop" => "groups", "type" => "link", "label" => "groups", "title" => "groups"));
    $out .= html::span(array("id" => "addresstabgroups", "class" => "tablink"), $button);
  }

  return $out;
  }

function cmail_contact_fields()
  {
  global $CONTACTS;

  $tabs = array('basic' => array('name', 'title', 'firstname', 'surname', 'email', 'altemail'),
                'home' => array('telephone', 'cellphone', 'fax', 'homeaddress'),
                'business' => array('organization', 'orgtelephone', 'orgfax', 'orgaddress'),
                'groups' => array(),
                'other' => array('birthday', 'notes'),
                );

  if (!array_key_exists('vcard', $CONTACTS->fieldmap)) {
    foreach ($tabs as $tab => $fields)
      $tabs[$tab] = array_intersect($fields, array_keys($CONTACTS->fieldmap));
  }

  return $tabs;
  }

function cmail_get_rowcount_text()
  {
  global $CONTACTS;
  
  // read nr of contacts
  $result = $CONTACTS->get_result();
  if (!$result)
    $result = $CONTACTS->count();
  
  if ($result->count == 0)
    $out = crystal_label('nocontactsfound');
  else
    $out = crystal_label(array(
      'name' => 'contactsfromto',
      'vars' => array(
        'from'  => $result->first + 1,
        'to'    => min($result->count, $result->first + $CONTACTS->page_size),
        'count' => $result->count)
      ));

  return $out;
  }
  
// register UI objects
$OUTPUT->add_handlers(array(
  'directorylist' => 'cmail_directory_list',
//  'groupslist' => 'cmail_contact_groups',
  'addresslist' => 'cmail_contacts_list',
  'addressframe' => 'cmail_contact_frame',
  'recordscountdisplay' => 'cmail_rowcount_display',
  'contacttabsbar' => 'cmail_contact_tabs',
  'searchform' => array($OUTPUT, 'search_form')
));


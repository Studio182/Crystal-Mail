<?php
/*
+----------------------------------------------------------------+
| ./program/steps/addressbook/show.inc                           |
|                                                                |
| This file was based on Roundcube Webmail Client                |
| Copyright (C) 2005-2009, RoundCube Dev. - Switzerland          |
|                                                                |
| This file is part of the Crystal Mail Client                   |
| Copyright (C) 2010, Crystal Mail Dev. Team - United States     |
|                                                                |
| Licensed under the GNU GPL                                     |
|                                                                |
| PURPOSE:                                                       |
|   Show contact details                                         |
|                                                                |
+--------------------- RoundCube Dev. Team ----------------------+
| Thomas Bruederli <roundcube@gmail.com>                         |
+----------------------- Studio 182 Team ------------------------+
| Hunter Dolan <hunter@crystalmail.net>                          |
+----------------------------------------------------------------+
*/



// read contact record
if (($cid = get_input_value('_cid', crystal_INPUT_GPC)) && ($record = $CONTACTS->get_record($cid, true))) {
  $OUTPUT->set_env('cid', $record['ID']);
}

function cmail_contact_details($attrib)
{
  global $CONTACTS, $OUTPUT;
  $microformats = array('name' => 'fn', 'email' => 'email');

  // check if we have a valid result
  if (!(($result = $CONTACTS->get_result()) && ($record = $result->first()))) {
    $OUTPUT->show_message('contactnotfound');
    return false;
  }
  $vcard = new crystal_vcard($record['vcard']);
  
  // a specific part is requested
  if ($attrib['part']) {
    return Q($record[$attrib['part']]);
  }

  $curtab = get_input_value('_tab', crystal_INPUT_GPC);
  $curtab = (empty($curtab)) ? 'basic' : $curtab;
  $OUTPUT->set_env('contacttab', $curtab);

  $panes = cmail_contact_fields();
  foreach ($panes as $pane => $a_show_cols) {
	if ($pane == 'groups') {
	  // return the complete address record as table
	  $table = new html_table(array('cols' => 2, 'id' => 'groups-table', 'cellspacing' => '0'));

	  disp_contact_groups($table, $record);

      $style = ($pane == $curtab) ? '' : 'display: none;';
      $container = html::tag('div', array('id' => 'group-manager'), $table->show($attrib + array('class' => 'records-table')));
      $out .= html::tag('div', array('id' => 'addressdiv' . $pane, 'style' => $style), $container);
	}
	else {
  // return the complete address record as table
  $table = new html_table(array('cols' => 2));

      foreach ($a_show_cols as $col)
        disp_contact_table($table, $record, $col, $microformats, $vcard);

      $style = ($pane == $curtab) ? '' : 'display: none;';
      $out .= html::tag('div', array('id' => 'addressdiv' . $pane, 'style' => $style), $table->show($attrib + array('class' => 'vcard')));
    }
  }

  return $out;
}

function disp_contact_table(&$table, $record, $col, $microformats, $vcard) {
  if ($col == 'email' && (!empty($record[$col]) || !empty($vcard->email[0]))) {
	$data = ($record[$col]) ? $record[$col] : $vcard->email[0];
      $value = html::a(array(
	     'href' => 'mailto:' . $data,
	     'onclick' => sprintf("return %s.command('compose','%s',this)", JS_OBJECT_NAME, JQ($data)),
        'title' => crystal_label('composeto'),
        'class' => $microformats[$col],
	), Q($data));
  }
  else if ($col == 'altemail' && (!empty($record[$col]) || !empty($vcard->email[1]))) {
    $data = ($record[$col]) ? array($record[$col]) : $vcard->email;

    for ($i = 1; $i < sizeof($data); $i++) {
      $value[] = html::a(array(
        'href' => 'mailto:' . $data[$i],
        'onclick' => sprintf("return %s.command('compose','%s',this)", JS_OBJECT_NAME, JQ($data[$i])),
        'title' => crystal_label('composeto'),
        'class' => $microformats[$col],
      ), Q($data[$i]));
    }
  }
  else if ($col == 'homeaddress' || $col == 'orgaddress') {
    $data = ($record[$col]) ? $record[$col] : $vcard->$col;
    if (is_array($data)) unset($data['type']);
    $value = is_array($data) ? join("<br />", $data) : str_replace("\n", "<br />", $data);
  }
  else if ($col == 'birthday') {
	$data = ($record[$col]) ? $record[$col] : $vcard->$col;
    $value = format_date(strtotime($data), cmail::get_instance()->config->get('date_only', 'd F Y'));
  }
  else if ($col == 'notes') {
	$data = ($record[$col]) ? $record[$col] : $vcard->$col;
    $value = str_replace("\n", "<br />", $data);
    }
    else if (!empty($record[$col])) {
      $value = html::span($microformats[$col], Q($record[$col]));
    }
  else if (!empty($vcard->$col)) {
    $value = html::span($microformats[$col], Q($vcard->$col));
  }

  if (!is_array($value))
    $value = array($value);

  foreach ($value as $idx => $val) {
    if ($col == 'altemail')
      $table->add(array('class' => 'title', 'style' => 'width: 150px; vertical-align: top;'), Q(str_replace("%n", $idx + 1, crystal_label($col))));
    else
      $table->add(array('class' => 'title', 'style' => 'width: 150px; vertical-align: top;'), Q(crystal_label($col)));
    
    $table->add(null, $val);
  }
}

function disp_contact_groups(&$table, $record)
{
  global $CONTACTS;
  $groups = $CONTACTS->list_groups();
  $cur_groups = $CONTACTS->contact_groups($record['ID']);

  $table->add_header('name', crystal_label('groupname'));
  $table->add_header('member', crystal_label('member'));

  foreach ($groups as $group) {
    $table->add('name', Q($group['name']));

    $checkbox_subscribe = new html_checkbox(array('name' => '_subscribed[]', 'value' => $group['ID'],
      'onclick' => JS_OBJECT_NAME.".command(this.checked?'add-group-member':'del-group-member',this.value)"));
  
    $selected = '';
    foreach ($cur_groups as $ingroup) {
	    if ($ingroup['ID'] == $group['ID']) {
		    $selected = $group['ID'];
		    break;
	    }
}

    $table->add('member', $checkbox_subscribe->show($selected));
  }
}

//$OUTPUT->framed = $_framed;
$OUTPUT->add_handler('contactdetails', 'cmail_contact_details');
if ($cmail->action == 'blank')
  $OUTPUT->send('blankcontact');
else
$OUTPUT->send('showcontact');

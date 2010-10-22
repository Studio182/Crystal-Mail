/**
 * ContextMenu plugin script
 */

cmail.contextmenu_command_handlers = new Object();
cmail.contextmenu_disable_multi = new Array('#reply','#reply-all','#forward','#print','#edit','#viewsource','#download','#open','#edit');

function rcm_contextmenu_update() {
	if (cmail.env.trash_mailbox && cmail.env.mailbox != cmail.env.trash_mailbox)
		$("#rcm_delete").html(cmail.gettext('movemessagetotrash'));
	else
		$("#rcm_delete").html(cmail.gettext('deletemessage'));
}

function rcm_contextmenu_init(row) {
	$("#" + row).contextMenu({
		menu: 'rcmContextMenu',
		submenu_delay: 400
	},
	function(command, el, pos) {
		var matches = String($(el).attr('id')).match(/rcmrow([a-z0-9\-_=]+)/i);
		if ($(el) && matches) {
			var prev_uid = cmail.env.uid;
			if (cmail.message_list.selection.length <= 1 || !cmail.message_list.in_selection(matches[1]))
				cmail.env.uid = matches[1];

			// fix command string in IE
			if (command.indexOf("#") > 0)
				command = command.substr(command.indexOf("#") + 1);

			// enable the required command
			cmd = (command == 'read' || command == 'unread' || command == 'flagged' || command == 'unflagged') ? 'mark' : command;
			var prev_command = cmail.commands[cmd];
			cmail.enable_command(cmd, true);

			// process external commands
			if (typeof cmail.contextmenu_command_handlers[command] == 'function') {
				cmail.contextmenu_command_handlers[command](command, el, pos);
			}
			else if (typeof cmail.contextmenu_command_handlers[command] == 'string') {
				window[cmail.contextmenu_command_handlers[command]](command, el, pos);
			}
			else {
				switch (command) {
					case 'read':
					case 'unread':
					case 'flagged':
					case 'unflagged':
						cmail.command('mark', command, $(el));
						break;
					case 'reply':
					case 'reply-all':
					case 'forward':
					case 'print':
					case 'download':
					case 'edit':
					case 'viewsource':
						cmail.command(command, '', $(el));
						break;
					case 'open':
						cmail.command(command, '', crystal_find_object('rcm_open'));
						cmail.sourcewin = window.open(crystal_find_object('rcm_open').href);
						if (cmail.sourcewin)
							window.setTimeout(function() { cmail.sourcewin.focus(); }, 20);

						crystal_find_object('rcm_open').href = '#open';
						break;
					case 'delete':
					case 'moveto':
						if (command == 'moveto' && cmail.env.rcm_destfolder == cmail.env.mailbox)
							return;

						var prev_sel = null;

						// also select childs of (collapsed) threads
						if (cmail.env.uid) {
							if (cmail.message_list.rows[cmail.env.uid].has_children && !cmail.message_list.rows[cmail.env.uid].expanded) {
								if (!cmail.message_list.in_selection(cmail.env.uid)) {
									prev_sel = cmail.message_list.get_selection();
									cmail.message_list.select_row(cmail.env.uid);
								}

								cmail.message_list.select_childs(cmail.env.uid);
								cmail.env.uid = null;
							}
							else if (!cmail.message_list.in_selection(cmail.env.uid)) {
								prev_sel = cmail.message_list.get_single_selection();
								cmail.message_list.remove_row(cmail.env.uid, false);
							}
							else if (cmail.message_list.get_single_selection() == cmail.env.uid) {
								cmail.env.uid = null;
							}
						}

						cmail.command(command, cmail.env.rcm_destfolder, $(el));

						if (prev_sel) {
							cmail.message_list.clear_selection();

							for (var i in prev_sel)
								cmail.message_list.select_row(prev_sel[i], CONTROL_KEY);
						}

						cmail.env.rcm_destfolder = null;
						break;
				}
			}

			cmail.enable_command(cmd, prev_command);
			cmail.env.uid = prev_uid;
		}
	});
}

function rcm_set_dest_folder(folder) {
	cmail.env.rcm_destfolder = folder;
}

function rcm_contextmenu_register_command(command, callback, label, pos, sep, multi, newSub, menu) {
	if (!menu)
		menu = $('#rcmContextMenu');

	if (typeof label != 'string') {
		var menuItem = label.children('li');
	}
	else {
		var menuItem = $('<li>').addClass(command);
		$('<a>').attr('href', '#' + command).addClass('active').html(cmail.gettext(label)).appendTo(menuItem);
	}

	cmail.contextmenu_command_handlers[command] = callback;

	if (pos && $('#rcmContextMenu .' + pos) && newSub) {
		subMenu = $('#rcmContextMenu .' + pos);
		subMenu.addClass('submenu');

		// remove any existing hyperlink
		if (subMenu.children('a')) {
			var text = subMenu.children('a').html();
			subMenu.html(text);
		}

		var newMenu = $('<ul>').addClass('toolbarmenu').appendTo(subMenu);
		newMenu.append(menuItem);
	}
	else if (pos && $('#rcmContextMenu .' + pos)) {
		$('#rcmContextMenu .' + pos).before(menuItem);
	}
	else {
		menu.append(menuItem);
	}

	if (sep == 'before')
		menuItem.addClass('separator_above');
	else if (sep == 'after')
		menuItem.addClass('separator_below');

	if (!multi)
		cmail.contextmenu_disable_multi[cmail.contextmenu_disable_multi.length] = '#' + command;
}

function rcm_foldermenu_init() {
	$("#mailboxlist-container li").contextMenu({
		menu: 'rcmFolderMenu'
	},
	function(command, el, pos) {
		var matches = String($(el).children('a').attr('onclick')).match(/.*cmail.command\(["']list["'],\s*["']([^"']*)["'],\s*this\).*/i);
		if ($(el) && matches) {
			var mailbox = matches[1];
			var messagecount = 0;

			if (command == 'readfolder' || command == 'expunge' || command == 'purge') {
				if (mailbox == cmail.env.mailbox) {
					messagecount = cmail.env.messagecount;
				}
				else if (cmail.env.unread_counts[mailbox] == 0) {
					cmail.set_busy(true, 'loading');

					querystring = '_mbox=' + urlencode(mailbox);
				    querystring += (querystring ? '&' : '') + '_remote=1';
				    var url = cmail.env.comm_path + '&_action=' + 'plugin.contextmenu.messagecount' + '&' + querystring

				    // send request
				    console.log('HTTP POST: ' + url);

				    jQuery.ajax({
				         url:    url,
				         dataType: "json",
				         success: function(response) { messagecount = response.env.messagecount; },
				         async:   false
				    });

				    cmail.set_busy(false);
				}

				if (cmail.env.unread_counts[mailbox] == 0 && messagecount == 0) {
					cmail.display_message(cmail.get_label('nomessagesfound'), 'notice');
					return false;
				}
			}

			// fix command string in IE
			if (command.indexOf("#") > 0)
				command = command.substr(command.indexOf("#") + 1);

			// enable the required command
			var prev_command = cmail.commands[command];
			cmail.enable_command(command, true);

			// process external commands
			if (typeof cmail.contextmenu_command_handlers[command] == 'function') {
				cmail.contextmenu_command_handlers[command](command, el, pos);
			}
			else if (typeof cmail.contextmenu_command_handlers[command] == 'string') {
				window[cmail.contextmenu_command_handlers[command]](command, el, pos);
			}
			else {
				switch (command) {
					case 'readfolder':
						cmail.set_busy(true, 'loading');
						cmail.http_request('plugin.contextmenu.readfolder', '_mbox=' + urlencode(mailbox) + '&_cur=' + cmail.env.mailbox, true);
						break;
					case 'expunge':
						cmail.expunge_mailbox(mailbox);
						break;
					case 'purge':
						cmail.purge_mailbox(mailbox);
						break;
					case 'collapseall':
					case 'expandall':
						targetdiv = (command == 'collapseall') ? 'expanded' : 'collapsed';
						$("#mailboxlist div." + targetdiv).each( function() {
							var el = $(this);
							var matches = String($(el).attr('onclick')).match(/.*cmail.command\(["']collapse-folder["'],\s*["']([^"']*)["']\).*/i);
							cmail.collapse_folder(matches[1]);
						});
						break;
					case 'openfolder':
						crystal_find_object('rcm_openfolder').href = '?_task=mail&_mbox='+urlencode(mailbox);
						cmail.sourcewin = window.open(crystal_find_object('rcm_openfolder').href);
						if (cmail.sourcewin)
							window.setTimeout(function() { cmail.sourcewin.focus(); }, 20);

						crystal_find_object('rcm_openfolder').href = '#openfolder';
						break;
				}
			}

			cmail.enable_command(command, prev_command);
		}
	});
}

function rcm_update_options(el) {
	if (el.hasClass('mailbox')) {
		$('#rcmFolderMenu').disableContextMenuItems('#readfolder,#purge,#collapseall,#expandall');
		var matches = String($(el).children('a').attr('onclick')).match(/.*cmail.command\(["']list["'],\s*["']([^"']*)["'],\s*this\).*/i);
		if ($(el) && matches) {
			var mailbox = matches[1];

			if (cmail.env.unread_counts[mailbox] > 0)
				$('#rcmFolderMenu').enableContextMenuItems('#readfolder');

			if (mailbox == cmail.env.trash_mailbox || mailbox == cmail.env.junk_mailbox
				|| mailbox.match('^' + RegExp.escape(cmail.env.trash_mailbox) + RegExp.escape(cmail.env.delimiter))
				|| mailbox.match('^' + RegExp.escape(cmail.env.junk_mailbox) + RegExp.escape(cmail.env.delimiter)))
					$('#rcmFolderMenu').enableContextMenuItems('#purge');

			if ($("#mailboxlist div.expanded").length > 0)
				$('#rcmFolderMenu').enableContextMenuItems('#collapseall');

			if ($("#mailboxlist div.collapsed").length > 0)
				$('#rcmFolderMenu').enableContextMenuItems('#expandall');
		}
	}
	else if (el.hasClass('addressbook') || el.hasClass('contactgroup')) {
		$('#rcmGroupMenu').disableContextMenuItems('#group-rename,#group-delete');

		if ($(el).hasClass('contactgroup')) {
			if (!cmail.name_input)
				$('#rcmGroupMenu').enableContextMenuItems('#group-rename');

			$('#rcmGroupMenu').enableContextMenuItems('#group-delete');
		}
	}
	else if (cmail.env.task == 'addressbook') {
		var matches = String($(el).attr('id')).match(/rcmrow([a-z0-9\-_=]+)/i);
		if (cmail.contact_list.selection.length > 1 && cmail.contact_list.in_selection(matches[1]))
			$('#rcmAddressMenu').disableContextMenuItems(cmail.contextmenu_disable_multi.join(','));
		else
			$('#rcmAddressMenu').enableContextMenuItems(cmail.contextmenu_disable_multi.join(','));

		if (cmail.env.address_sources[cmail.env.source].readonly)
			$('#rcmAddressMenu').disableContextMenuItems('#edit,#delete');
		else
			$('#rcmAddressMenu').enableContextMenuItems('#edit,#delete');
	}
	else {
		var matches = String($(el).attr('id')).match(/rcmrow([a-z0-9\-_=]+)/i);
		if (cmail.message_list.selection.length > 1 && cmail.message_list.in_selection(matches[1]))
			$('#rcmContextMenu').disableContextMenuItems(cmail.contextmenu_disable_multi.join(','));
		else
			$('#rcmContextMenu').enableContextMenuItems(cmail.contextmenu_disable_multi.join(','));
	}
}

function rcm_addressmenu_init(row) {
	$("#" + row).contextMenu({
		menu: 'rcmAddressMenu'
	},
	function(command, el, pos) {
		var matches = String($(el).attr('id')).match(/rcmrow([a-z0-9\-_=]+)/i);
		if ($(el) && matches) {
			var prev_cid = cmail.env.cid;
			if (cmail.contact_list.selection.length <= 1 || !cmail.contact_list.in_selection(matches[1]))
				cmail.env.cid = matches[1];

			// fix command string in IE
			if (command.indexOf("#") > 0)
				command = command.substr(command.indexOf("#") + 1);

			// enable the required command
			cmd = command;
			var prev_command = cmail.commands[cmd];
			cmail.enable_command(cmd, true);

			// process external commands
			if (typeof cmail.contextmenu_command_handlers[command] == 'function') {
				cmail.contextmenu_command_handlers[command](command, el, pos);
			}
			else if (typeof cmail.contextmenu_command_handlers[command] == 'string') {
				window[cmail.contextmenu_command_handlers[command]](command, el, pos);
			}
			else {
				switch (command) {
					case 'edit':
						cmail.contact_list.select(cmail.env.cid);
						clearTimeout(cmail.preview_timer)
						cmail.command(command, '', $(el));
						break;
					case 'compose':
					case 'delete':
					case 'moveto':
						if (command == 'moveto') {
							// check for valid taget
							if (cmail.env.rcm_destbook == cmail.env.source || cmail.env.contactfolders[cmail.env.rcm_destbook].id == cmail.env.group)
								return;
							// group restriction removed in r3694
							//else if (cmail.env.rcm_destgroup && cmail.env.rcm_destsource != cmail.env.source)
							//	return;
						}

						var prev_sel = null;

						if (cmail.env.cid) {
							if (!cmail.contact_list.in_selection(cmail.env.cid)) {
								prev_sel = cmail.contact_list.get_selection();
								cmail.contact_list.select(cmail.env.cid);

								if (!(command == 'moveto' && cmail.env.rcm_destbook.substring(0, 1) == 'G') && command != 'compose')
									cmail.contact_list.remove_row(cmail.env.cid, false);
							}
							else if (cmail.contact_list.get_single_selection() == cmail.env.cid) {
								cmail.env.cid = null;
							}
							else {
								prev_sel = cmail.contact_list.get_selection();
								cmail.contact_list.select(cmail.env.cid);
							}
						}

						cmail.drag_active = true;
						cmail.command(command, cmail.env.contactfolders[cmail.env.rcm_destbook], $(el));
						cmail.drag_active = false;

						if (prev_sel) {
							cmail.contact_list.clear_selection();

							for (var i in prev_sel)
								cmail.contact_list.select_row(prev_sel[i], CONTROL_KEY);
						}

						cmail.env.rcm_destbook = null;
						cmail.env.rcm_destsource = null;
						cmail.env.rcm_destgroup = null;
						break;
				}
			}

			cmail.enable_command(cmd, prev_command);
			cmail.env.cid = prev_cid;
		}
	});
}

function rcm_set_dest_book(obj, source, group) {
	cmail.env.rcm_destbook = obj;
	cmail.env.rcm_destsource = source;
	cmail.env.rcm_destgroup = group;
}

function rcm_groupmenu_init(li) {
	$(li).contextMenu({
		menu: 'rcmGroupMenu'
	},
	function(command, el, pos) {
		var matches = String($(el).children('a').attr('onclick')).match(/.*cmail.command\(["']listgroup["'],\s*({[^}]*}),\s*this\).*/i);
		if ($(el) && matches) {
			prev_group = cmail.env.group;
			prev_source = cmail.env.source;

			obj = eval('(' + matches[1] + ')');
			cmail.env.group = obj.id;
			cmail.env.source = obj.source;

			// fix command string in IE
			if (command.indexOf("#") > 0)
				command = command.substr(command.indexOf("#") + 1);

			// enable the required command
			var prev_command = cmail.commands[command];
			cmail.enable_command(command, true);

			// process external commands
			if (typeof cmail.contextmenu_command_handlers[command] == 'function') {
				cmail.contextmenu_command_handlers[command](command, el, pos);
			}
			else if (typeof cmail.contextmenu_command_handlers[command] == 'string') {
				window[cmail.contextmenu_command_handlers[command]](command, el, pos);
			}
			else {
				switch (command) {
					case 'group-rename':
						cmail.command(command, '', $(el).children('a'));

						// callback requires target is selected
						cmail.enable_command('listgroup', true);
						cmail.env.group = prev_group;
						cmail.env.source = prev_source
						prev_group = obj.id;
						prev_source = obj.source;;
						cmail.command('listgroup', {'source': prev_source,'id': prev_group}, $(el).children('a'));
						cmail.enable_command('listgroup', false);
						break;
					case 'group-delete':
						cmail.command(command, '', $(el).children('a'));
						break;
				}
			}

			cmail.enable_command(command, prev_command);
			cmail.env.group = prev_group;
			cmail.env.source = prev_source;
		}
	});
}

function rcm_groupmenu_update(action, props) {
	switch (action) {
		case 'insert':
			var link = $('<a>')
				.attr('id', 'rcm_contextgrps_G' + props.source + props.id)
				.attr('href', '#moveto')
				.addClass('active')
				.attr('onclick', "rcm_set_dest_book('G" + props.source + props.id + "', '" + props.source + "','" + props.id + "')")
				.html('&nbsp;&nbsp;' + props.name);

			var li = $('<li>').addClass('contactgroup').append(link);
			$(li).insertAfter($('#rcm_contextaddr_' + props.source));
			rcm_groupmenu_init(props.li);
			break;
		case 'update':
			if ($('#rcm_contextgrps_G' + props.source + props.id).length)
				$('#rcm_contextgrps_G' + props.source + props.id).html('&nbsp;&nbsp;' + props.name);

			break;
		case 'remove':
			if ($('#rcm_contextgrps_G' + props.source + props.id).length)
				$('#rcm_contextgrps_G' + props.source + props.id).remove();

			break;
	}
}

$(document).ready(function() {
	// init message list menu
	if ($('#rcmContextMenu').length > 0) {
		cmail.addEventListener('listupdate', function(props) { rcm_contextmenu_update(); } );
		cmail.addEventListener('insertrow', function(props) { rcm_contextmenu_init(props.row.id); } );
	}

	// init folder list menu
	if ($('#rcmFolderMenu').length > 0)
		cmail.add_onload('rcm_foldermenu_init();');

	// init contact list menu
	if ($('#rcmAddressMenu').length > 0)
		cmail.addEventListener('insertrow', function(props) { rcm_addressmenu_init(props.row.id); } );

	// init group list menu
	if ($('#rcmGroupMenu').length > 0) {
		cmail.add_onload('rcm_groupmenu_init("#directorylistbox li");');
		cmail.addEventListener('group_insert', function(props) { rcm_groupmenu_update('insert', props); } );
		cmail.addEventListener('group_update', function(props) { rcm_groupmenu_update('update', props); } );
		cmail.addEventListener('group_delete', function(props) { rcm_groupmenu_update('remove', props); } );
	}
});

/**
 * RoundCube functions for default skin interface
 */

/**
 * Settings
 */

function crystal_init_settings_tabs()
{
  var tab = '#settingslist span a';
  if (window.cmail && cmail.env.action)
    tab = '#settingslist span a' + (cmail.env.action=='preferences' ? 'default' : (cmail.env.action.indexOf('identity')>0 ? 'identities' : cmail.env.action.replace(/\./g, '')));

  $(tab).addClass('selected');
  $(tab + '> a').removeAttr('onclick').unbind('click').bind('click', function(){return false});
}

function crystal_show_advanced(visible)
{
  $('tr.advanced').css('display', (visible ? (bw.ie ? 'block' : 'table-row') : 'none'));
}

/**
 * Mail Composing
 */

function cmail_show_header_form(id)
{
  var link, row, parent, ns, ps;
  
  link = document.getElementById(id + '-link');
  parent = link.parentNode;

  if ((ns = cmail_next_sibling(link)))
    ns.style.display = 'none';
  else if ((ps = cmail_prev_sibling(link)))
    ps.style.display = 'none';
    
  link.style.display = 'none';

  if (row = document.getElementById('compose-' + id))
    {
    var div = document.getElementById('compose-body-container');
    var headers_div = document.getElementById('compose-container .header');
    row.style.display = (document.all && !window.opera) ? 'block' : 'table-row';
    div.style.top = (parseInt(headers_div.offsetHeight)) + 'px';
    }

  return false;
}

function cmail_hide_header_form(id)
{
  var row, parent, ns, ps, link, links;

  link = document.getElementById(id + '-link');
  link.style.display = '';
  
  parent = link.parentNode;
  links = parent.getElementsByTagName('a');

  for (var i=0; i<links.length; i++)
    if (links[i].style.display != 'none')
      for (var j=i+1; j<links.length; j++)
	if (links[j].style.display != 'none')
          if ((ns = cmail_next_sibling(links[i]))) {
	    ns.style.display = '';
	    break;
	  }

  document.getElementById('_' + id).value = '';

  if (row = document.getElementById('compose-' + id))
    {
    var div = document.getElementById('compose-body-container');
    var headers_div = document.getElementById('compose-container .header');
    row.style.display = 'none';
    div.style.top = (parseInt(headers_div.offsetHeight)) + 'px';
    }

  return false;
}

function cmail_next_sibling(elm)
{
  var ns = elm.nextSibling;
  while (ns && ns.nodeType == 3)
    ns = ns.nextSibling;
  return ns;
}

function cmail_prev_sibling(elm)
{
  var ps = elm.previousSibling;
  while (ps && ps.nodeType == 3)
    ps = ps.previousSibling;
  return ps;
}

function cmail_init_compose_form()
{
  var cc_field = document.getElementById('_cc');
  if (cc_field && cc_field.value!='')
    cmail_show_header_form('cc');

  var bcc_field = document.getElementById('_bcc');
  if (bcc_field && bcc_field.value!='')
    cmail_show_header_form('bcc');

  // prevent from form data loss when pressing ESC key in IE
  if (bw.ie) {
    var form = crystal_find_object('form');
    form.onkeydown = function (e) { if (crystal_event.get_keycode(e) == 27) crystal_event.cancel(e); };
  }
}

/**
 * Mailbox view
 */

function crystal_mail_ui()
{
  this.markmenu = $('#markmessagemenu');
  this.searchmenu = $('#searchmenu');
  this.messagemenu = $('#messagemenu');
}

crystal_mail_ui.prototype = {

show_markmenu: function(show)
{
  if (typeof show == 'undefined')
    show = this.markmenu.is(':visible') ? false : true;
  
  var ref = crystal_find_object('markreadbutton');
  if (show && ref)
    this.markmenu.css({ left:ref.offsetLeft, top:(ref.offsetTop + ref.offsetHeight) });
  
  this.markmenu[show?'show':'hide']();
},

show_messagemenu: function(show)
{
  if (typeof show == 'undefined')
    show = this.messagemenu.is(':visible') ? false : true;

  var ref = crystal_find_object('messagemenulink');
  if (show && ref)
    this.messagemenu.css({ left:ref.offsetLeft, top:(ref.offsetTop + ref.offsetHeight) });

  this.messagemenu[show?'show':'hide']();
},

show_searchmenu: function(show)
{
  if (typeof show == 'undefined')
    show = this.searchmenu.is(':visible') ? false : true;

  var ref = crystal_find_object('searchmod');
  if (show && ref) {
    var pos = $(ref).offset();
    this.searchmenu.css({ left:pos.left, top:(pos.top + ref.offsetHeight + 2)});

    if (cmail.env.search_mods) {
      for (var n in cmail.env.search_mods) {
        box = crystal_find_object('s_mod_' + n);
        box.checked = 'checked';
      }
    }
  }
  this.searchmenu[show?'show':'hide']();
},
 
set_searchmod: function(elem)
{
  if (!cmail.env.search_mods)
    cmail.env.search_mods = new Object();
  
  if (!elem.checked)
    delete(cmail.env.search_mods[elem.value]);
  else
    cmail.env.search_mods[elem.value] = elem.value;
},

body_mouseup: function(evt, p)
{
  if (this.markmenu && this.markmenu.is(':visible') && crystal_event.get_target(evt) != crystal_find_object('markreadbutton'))
    this.show_markmenu(false);
  else if (this.messagemenu && this.messagemenu.is(':visible') && crystal_event.get_target(evt) != crystal_find_object('messagemenulink'))
    this.show_messagemenu(false);
  else if (this.searchmenu && this.searchmenu.is(':visible') && crystal_event.get_target(evt) != crystal_find_object('searchmod')) {
    var menu = crystal_find_object('searchmenu');
    var target = crystal_event.get_target(evt);
    while (target.parentNode) {
      if (target.parentNode == menu)
        return;
      target = target.parentNode;
    }
    this.show_searchmenu(false);
  }
},

body_keypress: function(evt, p)
{
  if (crystal_event.get_keycode(evt) == 27) {
    if (this.markmenu && this.markmenu.is(':visible'))
      this.show_markmenu(false);
    if (this.searchmenu && this.searchmenu.is(':visible'))
      this.show_searchmenu(false);
    if (this.messagemenu && this.messagemenu.is(':visible'))
      this.show_messagemenu(false);
  }
}

};

var cmail_ui;

function crystal_init_mail_ui()
{
  cmail_ui = new crystal_mail_ui();
  crystal_event.add_listener({ object:cmail_ui, method:'body_mouseup', event:'mouseup' });
  crystal_event.add_listener({ object:cmail_ui, method:'body_keypress', event:'keypress' });
}

/* Sieve Filters (tab) */

if (window.cmail) {
  cmail.addEventListener('init', function(evt) {

    var tab = $('<span>').attr('id', 'settingstabpluginmanagesieve').addClass('tablink');
    var button = $('<a>').attr('href', cmail.env.comm_path+'&_action=plugin.managesieve')
      .attr('title', cmail.gettext('managesieve.managefilters'))
      .html(cmail.gettext('managesieve.filters'))
      .bind('click', function(e){ return cmail.command('plugin.managesieve', this) })
      .appendTo(tab);

    // add button and register commands
    cmail.add_element(tab, 'tabs');
    cmail.register_command('plugin.managesieve', function() { cmail.goto_url('plugin.managesieve') }, true);
    cmail.register_command('plugin.managesieve-save', function() { cmail.managesieve_save() }, true);
    cmail.register_command('plugin.managesieve-add', function() { cmail.managesieve_add() }, true);
    cmail.register_command('plugin.managesieve-del', function() { cmail.managesieve_del() }, true);
    cmail.register_command('plugin.managesieve-up', function() { cmail.managesieve_up() }, true);
    cmail.register_command('plugin.managesieve-down', function() { cmail.managesieve_down() }, true);
    cmail.register_command('plugin.managesieve-set', function() { cmail.managesieve_set() }, true);
    cmail.register_command('plugin.managesieve-setadd', function() { cmail.managesieve_setadd() }, true);
    cmail.register_command('plugin.managesieve-setdel', function() { cmail.managesieve_setdel() }, true);
    cmail.register_command('plugin.managesieve-setact', function() { cmail.managesieve_setact() }, true);
    cmail.register_command('plugin.managesieve-setget', function() { cmail.managesieve_setget() }, true);

    if (cmail.env.action == 'plugin.managesieve') {
      if (cmail.gui_objects.sieveform) {
        cmail.enable_command('plugin.managesieve-save', true);
      }
      else {
        cmail.enable_command('plugin.managesieve-del', 'plugin.managesieve-up',
          'plugin.managesieve-down', false);
        cmail.enable_command('plugin.managesieve-add', 'plugin.managesieve-setadd', !cmail.env.sieveconnerror);
      }

      if (cmail.gui_objects.filterslist) {
        var p = cmail;
        cmail.filters_list = new crystal_list_widget(cmail.gui_objects.filterslist, {multiselect:false, draggable:false, keyboard:false});
        cmail.filters_list.addEventListener('select', function(o){ p.managesieve_select(o); });
        cmail.filters_list.init();
        cmail.filters_list.focus();

        cmail.enable_command('plugin.managesieve-set', true);
        cmail.enable_command('plugin.managesieve-setact', 'plugin.managesieve-setget', cmail.gui_objects.filtersetslist.length);
        cmail.enable_command('plugin.managesieve-setdel', cmail.gui_objects.filtersetslist.length > 1);

        $('#'+cmail.buttons['plugin.managesieve-setact'][0].id).attr('title', cmail.gettext('managesieve.filterset'
          + (cmail.gui_objects.filtersetslist.value == cmail.env.active_set ? 'deact' : 'act')));
      }
    }
    if (cmail.gui_objects.sieveform && cmail.env.rule_disabled)
      $('#disabled').attr('checked', true);
  });
};

/*********************************************************/
/*********     Managesieve filters methods       *********/
/*********************************************************/

crystal_webmail.prototype.managesieve_add = function()
{
  this.load_managesieveframe();
  this.filters_list.clear_selection();
};

crystal_webmail.prototype.managesieve_del = function()
{
  var id = this.filters_list.get_single_selection();
  if (confirm(this.get_label('managesieve.filterdeleteconfirm')))
    this.http_request('plugin.managesieve',
      '_act=delete&_fid='+this.filters_list.rows[id].uid, true);
};

crystal_webmail.prototype.managesieve_up = function()
{
  var id = this.filters_list.get_single_selection();
  this.http_request('plugin.managesieve',
    '_act=up&_fid='+this.filters_list.rows[id].uid, true);
};

crystal_webmail.prototype.managesieve_down = function()
{
  var id = this.filters_list.get_single_selection();
  this.http_request('plugin.managesieve',
    '_act=down&_fid='+this.filters_list.rows[id].uid, true);
};

crystal_webmail.prototype.managesieve_rowid = function(id)
{
  var i, rows = this.filters_list.rows;

  for (i=0; i<rows.length; i++)
    if (rows[i] != null && rows[i].uid == id)
      return i;
}

crystal_webmail.prototype.managesieve_updatelist = function(action, name, id, disabled)
{
  this.set_busy(true);

  switch (action) {
    case 'delete':
      this.filters_list.remove_row(this.managesieve_rowid(id));
      this.filters_list.clear_selection();
      this.enable_command('plugin.managesieve-del', 'plugin.managesieve-up', 'plugin.managesieve-down', false);
      this.show_contentframe(false);

      // re-numbering filters
      var i, rows = this.filters_list.rows;
      for (i=0; i<rows.length; i++) {
        if (rows[i] != null && rows[i].uid > id)
          rows[i].uid = rows[i].uid-1;
      }
      break;

    case 'down':
      var from, fromstatus, status, rows = this.filters_list.rows;

      // we need only to replace filter names...
      for (var i=0; i<rows.length; i++) {
        if (rows[i]==null) { // removed row
          continue;
        }
        else if (rows[i].uid == id) {
          from = rows[i].obj;
          fromstatus = $(from).hasClass('disabled');
        }
        else if (rows[i].uid == id+1) {
          name = rows[i].obj.cells[0].innerHTML;
          status = $(rows[i].obj).hasClass('disabled');
          rows[i].obj.cells[0].innerHTML = from.cells[0].innerHTML;
          from.cells[0].innerHTML = name;
          $(from)[status?'addClass':'removeClass']('disabled');
          $(rows[i].obj)[fromstatus?'addClass':'removeClass']('disabled');
          this.filters_list.highlight_row(i);
          break;
        }
      }
      // ... and disable/enable Down button
      this.filters_listbuttons();
      break;

    case 'up':
      var from, status, fromstatus, rows = this.filters_list.rows;

      // we need only to replace filter names...
      for (var i=0; i<rows.length; i++) {
        if (rows[i] == null) { // removed row
          continue;
        }
        else if (rows[i].uid == id-1) {
          from = rows[i].obj;
          fromstatus = $(from).hasClass('disabled');
          this.filters_list.highlight_row(i);
        }
        else if (rows[i].uid == id) {
          name = rows[i].obj.cells[0].innerHTML;
          status = $(rows[i].obj).hasClass('disabled');
          rows[i].obj.cells[0].innerHTML = from.cells[0].innerHTML;
          from.cells[0].innerHTML = name;
          $(from)[status?'addClass':'removeClass']('disabled');
          $(rows[i].obj)[fromstatus?'addClass':'removeClass']('disabled');
          break;
        }
      }
      // ... and disable/enable Up button
      this.filters_listbuttons();
      break;

    case 'update':
      var rows = parent.cmail.filters_list.rows;
      for (var i=0; i<rows.length; i++)
        if (rows[i] && rows[i].uid == id) {
          rows[i].obj.cells[0].innerHTML = name;
          if (disabled)
            $(rows[i].obj).addClass('disabled');
          else
            $(rows[i].obj).removeClass('disabled');
          break;
        }
      break;

    case 'add':
      var row, new_row, td, list = parent.cmail.filters_list;

      if (!list)
        break;

      for (var i=0; i<list.rows.length; i++)
        if (list.rows[i] != null && String(list.rows[i].obj.id).match(/^rcmrow/))
          row = list.rows[i].obj;

      if (row) {
        new_row = parent.document.createElement('tr');
        new_row.id = 'rcmrow'+id;
        td = parent.document.createElement('td');
        new_row.appendChild(td);
        list.insert_row(new_row, false);
        if (disabled)
          $(new_row).addClass('disabled');
          if (row.cells[0].className)
            td.className = row.cells[0].className;

           td.innerHTML = name;
        list.highlight_row(id);

        parent.cmail.enable_command('plugin.managesieve-del', 'plugin.managesieve-up', true);
      }
      else // refresh whole page
        parent.cmail.goto_url('plugin.managesieve');
      break;
  }

  this.set_busy(false);
};

crystal_webmail.prototype.managesieve_select = function(list)
{
  var id = list.get_single_selection();
  if (id != null)
    this.load_managesieveframe(list.rows[id].uid);
};

crystal_webmail.prototype.managesieve_save = function()
{
  if (parent.cmail && parent.cmail.filters_list && this.gui_objects.sieveform.name != 'filtersetform') {
    var id = parent.cmail.filters_list.get_single_selection();
    if (id != null)
      this.gui_objects.sieveform.elements['_fid'].value = parent.cmail.filters_list.rows[id].uid;
  }
  this.gui_objects.sieveform.submit();
};

// load filter frame
crystal_webmail.prototype.load_managesieveframe = function(id)
{
  if (typeof(id) != 'undefined' && id != null) {
    this.enable_command('plugin.managesieve-del', true);
    this.filters_listbuttons();
  }
  else
    this.enable_command('plugin.managesieve-up', 'plugin.managesieve-down', 'plugin.managesieve-del', false);

  if (this.env.contentframe && window.frames && window.frames[this.env.contentframe]) {
    target = window.frames[this.env.contentframe];
    this.set_busy(true, 'loading');
    target.location.href = this.env.comm_path+'&_action=plugin.managesieve&_framed=1&_fid='+id;
  }
};

// enable/disable Up/Down buttons
crystal_webmail.prototype.filters_listbuttons = function()
{
  var id = this.filters_list.get_single_selection(),
    rows = this.filters_list.rows;

  for (var i=0; i<rows.length; i++) {
    if (rows[i] == null) { // removed row
    }
    else if (i == id) {
      this.enable_command('plugin.managesieve-up', false);
      break;
    }
    else {
      this.enable_command('plugin.managesieve-up', true);
      break;
    }
  }

  for (var i=rows.length-1; i>0; i--) {
    if (rows[i] == null) { // removed row
    }
    else if (i == id) {
      this.enable_command('plugin.managesieve-down', false);
      break;
    }
    else {
      this.enable_command('plugin.managesieve-down', true);
      break;
    }
  } 
};

// operations on filters form
crystal_webmail.prototype.managesieve_ruleadd = function(id)
{
  this.http_post('plugin.managesieve', '_act=ruleadd&_rid='+id);
};

crystal_webmail.prototype.managesieve_rulefill = function(content, id, after)
{
  if (content != '') {
    // create new element
    var div = document.getElementById('rules'),
      row = document.createElement('div');

    this.managesieve_insertrow(div, row, after);
    // fill row after inserting (for IE)
    row.setAttribute('id', 'rulerow'+id);
    row.className = 'rulerow';
    row.innerHTML = content;

    this.managesieve_formbuttons(div);
  }
};

crystal_webmail.prototype.managesieve_ruledel = function(id)
{
  if (confirm(this.get_label('managesieve.ruledeleteconfirm'))) {
    var row = document.getElementById('rulerow'+id);
    row.parentNode.removeChild(row);
    this.managesieve_formbuttons(document.getElementById('rules'));
  }
};

crystal_webmail.prototype.managesieve_actionadd = function(id)
{
  this.http_post('plugin.managesieve', '_act=actionadd&_aid='+id);
};

crystal_webmail.prototype.managesieve_actionfill = function(content, id, after)
{
  if (content != '') {
    var div = document.getElementById('actions'),
      row = document.createElement('div');

    this.managesieve_insertrow(div, row, after);
    // fill row after inserting (for IE)
    row.className = 'actionrow';
    row.setAttribute('id', 'actionrow'+id);
    row.innerHTML = content;

    this.managesieve_formbuttons(div);
  }
};

crystal_webmail.prototype.managesieve_actiondel = function(id)
{
  if (confirm(this.get_label('managesieve.actiondeleteconfirm'))) {
    var row = document.getElementById('actionrow'+id);
    row.parentNode.removeChild(row);
    this.managesieve_formbuttons(document.getElementById('actions'));
  }
};

// insert rule/action row in specified place on the list
crystal_webmail.prototype.managesieve_insertrow = function(div, row, after)
{
  for (var i=0; i<div.childNodes.length; i++) {
    if (div.childNodes[i].id == (div.id == 'rules' ? 'rulerow' : 'actionrow')  + after)
      break;
  }

  if (div.childNodes[i+1])
    div.insertBefore(row, div.childNodes[i+1]);
  else
    div.appendChild(row);
};

// update Delete buttons status
crystal_webmail.prototype.managesieve_formbuttons = function(div)
{
  var i, button, buttons = [];

  // count and get buttons
  for (i=0; i<div.childNodes.length; i++) {
    if (div.id == 'rules' && div.childNodes[i].id) {
      if (/rulerow/.test(div.childNodes[i].id))
        buttons.push('ruledel' + div.childNodes[i].id.replace(/rulerow/, ''));
    }
    else if (div.childNodes[i].id) {
      if (/actionrow/.test(div.childNodes[i].id))
        buttons.push( 'actiondel' + div.childNodes[i].id.replace(/actionrow/, ''));
    }
  }

  for (i=0; i<buttons.length; i++) {
    button = document.getElementById(buttons[i]);
    if (i>0 || buttons.length>1) {
      $(button).removeClass('disabled');
      button.removeAttribute('disabled');
    }
    else {
      $(button).addClass('disabled');
      button.setAttribute('disabled', true);
    }
  }
};

// Set change
crystal_webmail.prototype.managesieve_set = function()
{
  var script = $(this.gui_objects.filtersetslist).val();
  location.href = this.env.comm_path+'&_action=plugin.managesieve&_set='+script;
};

// Script download
crystal_webmail.prototype.managesieve_setget = function()
{
  var script = $(this.gui_objects.filtersetslist).val();
  location.href = this.env.comm_path+'&_action=plugin.managesieve&_act=setget&_set='+script;
};

// Set activate
crystal_webmail.prototype.managesieve_setact = function()
{
  if (!this.gui_objects.filtersetslist)
    return false;

  var script = this.gui_objects.filtersetslist.value,
    action = (script == cmail.env.active_set ? 'deact' : 'setact');

  this.http_post('plugin.managesieve', '_act='+action+'&_set='+script);
};

// Set activate flag in sets list after set activation
crystal_webmail.prototype.managesieve_reset = function()
{
  if (!this.gui_objects.filtersetslist)
    return false;

  var list = this.gui_objects.filtersetslist,
    opts = list.getElementsByTagName('option'),
    label = ' (' + this.get_label('managesieve.active') + ')',
    regx = new RegExp(RegExp.escape(label)+'$');

  for (var x=0; x<opts.length; x++) {
    if (opts[x].value != cmail.env.active_set && opts[x].innerHTML.match(regx))
      opts[x].innerHTML = opts[x].innerHTML.replace(regx, '');
    else if (opts[x].value == cmail.env.active_set)
      opts[x].innerHTML = opts[x].innerHTML + label;
  }

  // change title of setact button
  $('#'+cmail.buttons['plugin.managesieve-setact'][0].id).attr('title', cmail.gettext('managesieve.filterset'
    + (list.value == cmail.env.active_set ? 'deact' : 'act')));
};

// Set delete
crystal_webmail.prototype.managesieve_setdel = function()
{
  if (!this.gui_objects.filtersetslist)
    return false;

  if (!confirm(this.get_label('managesieve.setdeleteconfirm')))
    return false;

  var script = this.gui_objects.filtersetslist.value;
  this.http_post('plugin.managesieve', '_act=setdel&_set='+script);
};

// Set add
crystal_webmail.prototype.managesieve_setadd = function()
{
  this.filters_list.clear_selection();
  this.enable_command('plugin.managesieve-up', 'plugin.managesieve-down', 'plugin.managesieve-del', false);

  if (this.env.contentframe && window.frames && window.frames[this.env.contentframe]) {
    target = window.frames[this.env.contentframe];
    this.set_busy(true, 'loading');
    target.location.href = this.env.comm_path+'&_action=plugin.managesieve&_framed=1&_newset=1';
  }
};

crystal_webmail.prototype.managesieve_reload = function(set)
{
  this.env.reload_set = set;
  window.setTimeout(function() {
    location.href = cmail.env.comm_path + '&_action=plugin.managesieve'
      + (cmail.env.reload_set ? '&_set=' + cmail.env.reload_set : '')
  }, 500);
};


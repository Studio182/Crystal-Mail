/* Show user-info plugin script */

if (window.cmail) {
  cmail.addEventListener('init', function(evt) {
    // <span id="settingstabdefault" class="tablink"><crystalmail:button command="preferences" type="link" label="preferences" title="editpreferences" /></span>
    var tab = $('<span>').attr('id', 'settingstabpluginabout').addClass('tablink');
    
    var button = $('<a>').attr('href', cmail.env.comm_path+'&_action=plugin.about').html(cmail.gettext('about', 'about')).appendTo(tab);
    button.bind('click', function(e){ return cmail.command('plugin.about', this) });
    
    // add button and register command
    cmail.add_element(tab, 'tabs');
    cmail.register_command('plugin.about', function(){ cmail.goto_url('plugin.about') }, true);
  })
}


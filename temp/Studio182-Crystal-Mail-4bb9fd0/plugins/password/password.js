/*
 * Password plugin script
 * @version @package_version@
 */

if (window.cmail) {
  cmail.addEventListener('init', function(evt) {
    // <span id="settingstabdefault" class="tablink"><crystalmail:button command="preferences" type="link" label="preferences" title="editpreferences" /></span>
    var tab = $('<span>').attr('id', 'settingstabpluginpassword').addClass('tablink');
    
    var button = $('<a>').attr('href', cmail.env.comm_path+'&_action=plugin.password').html(cmail.gettext('password')).appendTo(tab);
    button.bind('click', function(e){ return cmail.command('plugin.password', this) });

    // add button and register commands
    cmail.add_element(tab, 'tabs');
    cmail.register_command('plugin.password', function() { cmail.goto_url('plugin.password') }, true);
    cmail.register_command('plugin.password-save', function() { 
      var input_curpasswd = crystal_find_object('_curpasswd');
      var input_newpasswd = crystal_find_object('_newpasswd');
          var input_confpasswd = crystal_find_object('_confpasswd');
    
      if (input_curpasswd && input_curpasswd.value=='') {
          alert(cmail.gettext('nocurpassword', 'password'));
          input_curpasswd.focus();
      } else if (input_newpasswd && input_newpasswd.value=='') {
          alert(cmail.gettext('nopassword', 'password'));
          input_newpasswd.focus();
      } else if (input_confpasswd && input_confpasswd.value=='') {
          alert(cmail.gettext('nopassword', 'password'));
          input_confpasswd.focus();
      } else if (input_newpasswd && input_confpasswd && input_newpasswd.value != input_confpasswd.value) {
          alert(cmail.gettext('passwordinconsistency', 'password'));
          input_newpasswd.focus();
      } else {
          cmail.gui_objects.passform.submit();
      }
    }, true);
  })
}

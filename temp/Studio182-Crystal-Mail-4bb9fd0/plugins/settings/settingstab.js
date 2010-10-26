// Settingstab Account

if (window.cmail) {
  cmail.addEventListener('init', function(evt) {
    var tab = $('<span>').attr('id', 'settingstabaccount').addClass('tablink');   
    var button = $('<a>').attr('href', cmail.env.comm_path+'&_action=plugin.account').html(cmail.gettext('account','settings')).appendTo(tab);
    button.bind('click', function(e){ return cmail.command('plugin.account', this) });

    // add button and register commands
    cmail.add_element(tab, 'tabs');
    cmail.register_command('plugin.account', function() { cmail.goto_url('plugin.account') }, true);     

  }
)}
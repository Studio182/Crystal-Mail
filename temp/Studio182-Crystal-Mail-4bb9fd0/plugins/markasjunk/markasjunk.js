/* Mark-as-Junk plugin script */

function cmail_markasjunk(prop)
{
  if (!cmail.env.uid && (!cmail.message_list || !cmail.message_list.get_selection().length))
    return;
  
    var uids = cmail.env.uid ? cmail.env.uid : cmail.message_list.get_selection().join(',');
    
    cmail.set_busy(true, 'loading');
    cmail.http_post('plugin.markasjunk', '_uid='+uids+'&_mbox='+urlencode(cmail.env.mailbox), true);
}

// callback for app-onload event
if (window.cmail) {
  cmail.addEventListener('init', function(evt) {
    
    // register command (directly enable in message view mode)
    cmail.register_command('plugin.markasjunk', cmail_markasjunk, cmail.env.uid);
    
    // add event-listener to message list
    if (cmail.message_list)
      cmail.message_list.addEventListener('select', function(list){
        cmail.enable_command('plugin.markasjunk', list.get_selection().length > 0);
      });
  })
}


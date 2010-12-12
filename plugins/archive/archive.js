/*
 * Archive plugin script
 * @version @package_version@
 */

function cmail_archive(prop)
{
  if (!cmail.env.uid && (!cmail.message_list || !cmail.message_list.get_selection().length))
    return;
  
  var uids = cmail.env.uid ? cmail.env.uid : cmail.message_list.get_selection().join(',');
    
  cmail.set_busy(true, 'loading');
  cmail.http_post('plugin.archive', '_uid='+uids+'&_mbox='+urlencode(cmail.env.mailbox), true);
}

// callback for app-onload event
if (window.cmail) {
  cmail.addEventListener('init', function(evt) {
    
    // register command (directly enable in message view mode)
    cmail.register_command('plugin.archive', cmail_archive, (cmail.env.uid && cmail.env.mailbox != cmail.env.archive_folder));
    
    // add event-listener to message list
    if (cmail.message_list)
      cmail.message_list.addEventListener('select', function(list){
        cmail.enable_command('plugin.archive', (list.get_selection().length > 0 && cmail.env.mailbox != cmail.env.archive_folder));
      });
    
    // set css style for archive folder
    var li;
    if (cmail.env.archive_folder && cmail.env.archive_folder_icon && (li = cmail.get_folder_li(cmail.env.archive_folder)))
      $(li).css('background-image', 'url(' + cmail.env.archive_folder_icon + ')');
  })
}


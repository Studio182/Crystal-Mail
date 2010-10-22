/*
 * vcard_attachments plugin script
 * @version @package_version@
 */
function plugin_vcard_save_contact(mime_id)
{
  cmail.set_busy(true, 'loading');
  cmail.http_post('plugin.savevcard', '_uid='+cmail.env.uid+'&_mbox='+urlencode(cmail.env.mailbox)+'&_part='+urlencode(mime_id), true);
  
  return false;
}



<?php

/**
 * ZipDownload
 *
 * Plugin to allow the download of all message attachments in one zip file
 *
 * @version 1.1
 * @author Philip Weir
 */
class zipdownload extends crystal_plugin
{
	public $task = 'mail';

	function init()
	{
		$cmail = cmail::get_instance();
		if ($cmail->action == 'show' || $cmail->action == 'preview')
			$this->add_hook('template_object_messageattachments', array($this, 'attachment_ziplink'));

		$this->register_action('plugin.zipdownload.zip_attachments', array($this, 'download_attachments'));
	}

	function attachment_ziplink($p)
	{
		// only show the link if there is more than 1 attachment
		if (substr_count($p['content'], '<li>') > 1) {
			$cmail = cmail::get_instance();
			$this->add_texts('localization');

			$link = html::tag('li', null,
				html::a(array(
					'href' => cmail_url('plugin.zipdownload.zip_attachments', array('_mbox' => $cmail->output->env['mailbox'], '_uid' => $cmail->output->env['uid'])),
					'title' => $this->gettext('downloadall'),
					),
					html::img(array('src' => $this->url(null) . $this->local_skin_path() . '/zip.png', 'alt' => $this->gettext('downloadall'), 'border' => 0)))
				);

			$p['content'] = preg_replace('/(<ul[^>]*>)/', '$1' . $link, $p['content']);
		}

		return $p;
	}

	function download_attachments()
	{
		$cmail = cmail::get_instance();
		$imap = $cmail->imap;
		$temp_dir = $cmail->config->get('temp_dir');
		$tmpfname = tempnam($temp_dir, 'attachments');
		$message = new crystal_message(get_input_value('_uid', crystal_INPUT_GET));

		// open zip file
		$zip = new ZipArchive();
		$zip->open($tmpfname, ZIPARCHIVE::OVERWRITE);

		foreach ($message->attachments as $part) {
			$pid = $part->mime_id;
			$part = $message->mime_parts[$pid];

			if ($part->body)
				$orig_message_raw = $part->body;
			else
				$orig_message_raw = $imap->get_message_part($message->uid, $part->mime_id, $part);

			$zip->addFromString($part->filename, $orig_message_raw);
		}

		$zip->close();

		$browser = new crystal_browser;
		send_nocacheing_headers();

		$filename = ($message->subject ? $message->subject : 'crystalmail') . '.zip';

		if ($browser->ie && $browser->ver < 7)
			$filename = rawurlencode(abbreviate_string($filename, 55));
		else if ($browser->ie)
			$filename = rawurlencode($filename);
		else
			$filename = addcslashes($filename, '"');

		// send download headers
		header("Content-Type: application/octet-stream");
		if ($browser->ie)
			header("Content-Type: application/force-download");

		// don't kill the connection if download takes more than 30 sec.
		@set_time_limit(0);
		header("Content-Disposition: attachment; filename=\"". $filename ."\"");
		header("Content-length: " . filesize($tmpfname));
		readfile($tmpfname);

		// delete zip file
		unlink($tmpfname);

		exit;
	}
}

?>
<?php

/**
 * Youtube
 *
 * Replace youtube links with embed code
 *
 * @version 0.2
 * @author James Riach
 */
class youtube extends crystal_plugin
{
	public $task = 'mail';
	
	function init()
	{
		$this->task = 'mail';
		$this->add_hook('message_part_after', array($this, 'utube'));
		
		$this->search = '/<a href="http:\/\/www\.youtube\.com\/watch\?v=(\S*?)"\ target=\"_blank\">http:\/\/www\.youtube\.com\/watch\?v=(\S*?)\<\/a>/im';
		$this->search2 = array('/<p>http:\/\/www\.youtube\.com\/watch\?v=(\S*?)\<\/p>/im', '/<a class="moz-txt-link-freetext" href="http:\/\/www\.youtube\.com\/watch\?v=(\S*?)">http:\/\/www\.youtube\.com\/watch\?v=(\S*?)<\/a>/im', '/<a href="http:\/\/www\.youtube\.com\/watch\?v=(\S*?)">http:\/\/www\.youtube\.com\/watch\?v=(\S*?)\<\/a>/im');
		$this->youtube = '<div style="text-align:left";><object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/\\1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/\\1" type="application/x-shockwave-flash" wmod="transparent" width="425" height="344"></embed></object></div>';
	}

	function utube($args)
	{
			return array('body' => preg_replace($this->search2, $this->youtube, $args['body']));
			
		return null;
	}

}

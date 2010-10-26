<?php

/**
 * Help Plugin
 *
 * @author Aleksander 'A.L.E.C' Machniak
 * @licence GNU GPL
 *
 * Configuration (see config.inc.php.dist)
 * 
 **/

class help extends crystal_plugin
{
    // all task excluding 'login' and 'logout'
    public $task = '?(?!login|logout).*';

    function init()
    {
        $cmail = cmail::get_instance();

        $this->add_texts('localization/', false);

        // register actions
        $this->register_action('plugin.help', array($this, 'action'));
        $this->register_action('plugin.helpabout', array($this, 'action'));
        $this->register_action('plugin.helplicense', array($this, 'action'));

        // add taskbar button
        $this->add_button(array(
	        'name' 	=> 'helptask',
	        'class'	=> 'button-help',
	        'label'	=> 'help.help',
	        'href'	=> './?_task=dummy&_action=plugin.help',
            ), 'taskbar');

        $skin = $cmail->config->get('skin');
        if (!file_exists($this->home."/skins/$skin/help.css"))
	        $skin = 'default';

        // add style for taskbar button (must be here) and Help UI    
        $this->include_stylesheet("skins/$skin/help.css");
    }

    function action()
    {
        $cmail = cmail::get_instance();

        $this->load_config();

        // register UI objects
        $cmail->output->add_handlers(array(
	        'helpcontent' => array($this, 'content'),
        ));

        if ($cmail->action == 'plugin.helpabout')
	        $cmail->output->set_pagetitle($this->gettext('about'));
        else if ($cmail->action == 'plugin.helplicense')
            $cmail->output->set_pagetitle($this->gettext('license'));
        else
            $cmail->output->set_pagetitle($this->gettext('help'));

        $cmail->output->send('help.help');
    }

    function content($attrib)
    {
        $cmail = cmail::get_instance();

        if ($cmail->action == 'plugin.helpabout') {
	        return @file_get_contents($this->home.'/content/about.html');
        }
        else if ($cmail->action == 'plugin.helplicense') {
	        return @file_get_contents($this->home.'/content/license.html');
        }

        // default content: iframe

        if ($src = $cmail->config->get('help_source'))
	        $attrib['src'] = $src;

        if (empty($attrib['id']))
            $attrib['id'] = 'cmailhelpcontent';
    
        // allow the following attributes to be added to the <iframe> tag
        $attrib_str = create_attrib_string($attrib, array(
            'id', 'class', 'style', 'src', 'width', 'height', 'frameborder'));

        $out = sprintf('<iframe name="%s"%s></iframe>'."\n", $attrib['id'], $attrib_str);
    
        return $out;
    }

}

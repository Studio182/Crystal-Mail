<?php

/**
 * Subscription Options
 *
 * A plugin which can enable or disable the use of imap subscriptions.
 * It includes a toggle on the settings page under "Server Settings".
 * The preference can also be locked
 *
 * Add it to the plugins list in config/main.inc.php to enable the user option
 * The user option can be hidden and set globally by adding 'use_subscriptions'
 * to the the 'dont_override' configure line:
 * $cmail_config['dont_override'] = array('use_subscriptions');
 * and then set the global preference
 * $cmail_config['use_subscriptions'] = true; // or false
 *
 * crystalmail caches folder lists.  When a user changes this option or visits
 * their folder list, this cache is refreshed.  If the option is on the
 * 'dont_override' list and the global option has changed, don't expect
 * to see the change until the folder list cache is refreshed.
 *
 * @version 1.0
 * @author Ziba Scott
 */
class subscriptions_option extends crystal_plugin
{
    public $task = 'mail|settings';
    
    function init()
    {
        $this->add_texts('localization/', false);
        $dont_override = cmail::get_instance()->config->get('dont_override', array());
        if (!in_array('use_subscriptions', $dont_override)) {
            $this->add_hook('preferences_list', array($this, 'settings_blocks'));
            $this->add_hook('preferences_save', array($this, 'save_prefs'));
        }
        $this->add_hook('mailboxes_list', array($this, 'mailboxes_list'));
        $this->add_hook('folders_list', array($this, 'folders_list'));
    }

    function settings_blocks($args)
    {
        if ($args['section'] == 'server') {
            $use_subscriptions = cmail::get_instance()->config->get('use_subscriptions');
            $field_id = 'rcmfd_use_subscriptions';
            $checkbox = new html_checkbox(array('name' => '_use_subscriptions', 'id' => $field_id, 'value' => 1));

            $args['blocks']['main']['options']['use_subscriptions'] = array(
                'title' => html::label($field_id, Q($this->gettext('useimapsubscriptions'))),
                'content' => $checkbox->show($use_subscriptions?1:0),
            );
        }

        return $args;
    }

    function save_prefs($args)
    {
        if ($args['section'] == 'server') {
            $cmail = cmail::get_instance();
            $use_subscriptions = $cmail->config->get('use_subscriptions');

            $args['prefs']['use_subscriptions'] = isset($_POST['_use_subscriptions']) ? true : false;

            // if the use_subscriptions preference changes, flush the folder cache
            if (($use_subscriptions && !isset($_POST['_use_subscriptions'])) ||
                (!$use_subscriptions && isset($_POST['_use_subscriptions']))) {
                    $cmail->imap_connect();
                    $cmail->imap->clear_cache('mailboxes');
            }
        }
        return $args;
    }

    function mailboxes_list($args)
    {
        $cmail = cmail::get_instance();
        if (!$cmail->config->get('use_subscriptions', true)) {
            $args['folders'] = $cmail->imap->conn->listMailboxes($cmail->imap->mod_mailbox($args['root']), $args['filter']);
        }
        return $args;
    }

    function folders_list($args)
    {
        $cmail = cmail::get_instance();
        if (!$cmail->config->get('use_subscriptions', true)) {
            $args['table']->remove_column('subscribed');
        }
        return $args;
    }
}

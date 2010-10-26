<?php

/**
 * Google contacts backend
 *
 * Minimal backend for Google contacts
 *
 * @author Roland 'rosali' Liebl
 * @version 1.0
 */

class google_contacts_backend extends crystal_contacts
{
    function __construct($dbconn, $user)
    {
        //next two lines are 0.4-beta fix (not needed since r3562)
        $cmail = cmail::get_instance();
        $cmail->config->set('db_table_contacts', $cmail->config->get('db_table_google_contacts'));
        parent::__construct($dbconn, $user);
        $this->db_name = get_table_name('google_contacts');
    }
}
?>
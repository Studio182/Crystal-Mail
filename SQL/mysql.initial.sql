-- Crystal Mail DB Structure


/*!40014  SET FOREIGN_KEY_CHECKS=0 */;

-- Table structure for table `session`

CREATE TABLE `session` (
 `sess_id` varchar(40) NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `ip` varchar(40) NOT NULL,
 `vars` mediumtext NOT NULL,
 PRIMARY KEY(`sess_id`),
 INDEX `changed_index` (`changed`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `users`

CREATE TABLE `users` (
 `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `username` varchar(128) NOT NULL,
 `mail_host` varchar(128) NOT NULL,
 `alias` varchar(128) NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `last_login` datetime DEFAULT NULL,
 `language` varchar(5),
 `preferences` text,
 PRIMARY KEY(`user_id`),
 INDEX `username_index` (`username`),
 INDEX `alias_index` (`alias`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `messages`

CREATE TABLE `messages` (
 `message_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `cache_key` varchar(128) /*!40101 CHARACTER SET ascii COLLATE ascii_general_ci */ NOT NULL,
 `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `idx` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `uid` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `subject` varchar(255) NOT NULL,
 `from` varchar(255) NOT NULL,
 `to` varchar(255) NOT NULL,
 `cc` varchar(255) NOT NULL,
 `date` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `size` int(11) UNSIGNED NOT NULL DEFAULT '0',
 `headers` text NOT NULL,
 `structure` text,
 PRIMARY KEY(`message_id`),
 CONSTRAINT `user_id_fk_messages` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `created_index` (`created`),
 INDEX `index_index` (`user_id`, `cache_key`, `idx`),
 UNIQUE `uniqueness` (`user_id`, `cache_key`, `uid`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `cache`

CREATE TABLE `cache` (
 `data` longtext NOT NULL,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`cache_id`),
 CONSTRAINT `user_id_fk_cache` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `created_index` (`created`),
 INDEX `user_cache_index` (`user_id`,`cache_key`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


-- Table structure for table `contacts`

CREATE TABLE `contacts` (
 `contact_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `name` varchar(128) NOT NULL DEFAULT '',
 `email` varchar(128) NOT NULL,
 `firstname` varchar(128) NOT NULL DEFAULT '',
 `surname` varchar(128) NOT NULL DEFAULT '',
 `vcard` text NULL,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 PRIMARY KEY(`contact_id`),
 CONSTRAINT `user_id_fk_contacts` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `user_contacts_index` (`user_id`,`email`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

-- Table structure for table `contactgroups`

CREATE TABLE `contactgroups` (
  `contactgroup_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `del` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY(`contactgroup_id`),
  CONSTRAINT `user_id_fk_contactgroups` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `contactgroups_user_index` (`user_id`,`del`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;

CREATE TABLE `contactgroupmembers` (
  `contactgroup_id` int(10) UNSIGNED NOT NULL,
  `contact_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  PRIMARY KEY (`contactgroup_id`, `contact_id`),
  CONSTRAINT `contactgroup_id_fk_contactgroups` FOREIGN KEY (`contactgroup_id`)
    REFERENCES `contactgroups`(`contactgroup_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `contact_id_fk_contacts` FOREIGN KEY (`contact_id`)
    REFERENCES `contacts`(`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) /*!40000 ENGINE=INNODB */;


-- Table structure for table `identities`

CREATE TABLE `identities` (
 `identity_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
 `changed` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
 `del` tinyint(1) NOT NULL DEFAULT '0',
 `standard` tinyint(1) NOT NULL DEFAULT '0',
 `name` varchar(128) NOT NULL,
 `organization` varchar(128) NOT NULL DEFAULT '',
 `email` varchar(128) NOT NULL,
 `reply-to` varchar(128) NOT NULL DEFAULT '',
 `bcc` varchar(128) NOT NULL DEFAULT '',
 `signature` text,
 `html_signature` tinyint(1) NOT NULL DEFAULT '0',
 PRIMARY KEY(`identity_id`),
 CONSTRAINT `user_id_fk_identities` FOREIGN KEY (`user_id`)
   REFERENCES `users`(`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
 INDEX `user_identities_index` (`user_id`, `del`)
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;


/*!40014 SET FOREIGN_KEY_CHECKS=1 */;

CREATE TABLE `events` (
  `event_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `start` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `end` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
  `summary` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL DEFAULT '',
  `categories` varchar(255) NOT NULL DEFAULT '',
  `all_day` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY(`event_id`),
  CONSTRAINT `user_id_fk_events` FOREIGN KEY (`user_id`)
    REFERENCES `users`(`user_id`)
    /*!40008
      ON DELETE CASCADE
      ON UPDATE CASCADE */
) /*!40000 ENGINE=INNODB */ /*!40101 CHARACTER SET utf8 COLLATE utf8_general_ci */;
CREATE TABLE IF NOT EXISTS `accounts` (
  `aid` int(10) unsigned NOT NULL auto_increment,
  `account_dn` varchar(128) NOT NULL,  
  `account_id` varchar(128) NOT NULL,
  `account_pw` varchar(128) NOT NULL,
  `account_host` varchar(128) NOT NULL,
  `preferences` text,
  `user_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`aid`),
  KEY `user_id_fk_accounts` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

CREATE TABLE google_contacts LIKE contacts;

ALTER TABLE `google_contacts`
  ADD CONSTRAINT `google_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

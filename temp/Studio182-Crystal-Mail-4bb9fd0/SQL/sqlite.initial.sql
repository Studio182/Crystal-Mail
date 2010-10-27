-- Crystal Mail DB Structure

-- 
-- Table structure for table `cache`
-- 

CREATE TABLE cache (
  cache_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default 0,
  cache_key varchar(128) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  data longtext NOT NULL
);

CREATE INDEX ix_cache_user_cache_key ON cache(user_id, cache_key);
CREATE INDEX ix_cache_created ON cache(created);


-- --------------------------------------------------------

-- 
-- Table structure for table contacts and related
-- 

CREATE TABLE contacts (
  contact_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  changed datetime NOT NULL default '0000-00-00 00:00:00',
  del tinyint NOT NULL default '0',
  name varchar(128) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  surname varchar(128) NOT NULL default '',
  vcard text NOT NULL default ''
);

CREATE INDEX ix_contacts_user_id ON contacts(user_id, email);


CREATE TABLE contactgroups (
  contactgroup_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  changed datetime NOT NULL default '0000-00-00 00:00:00',
  del tinyint NOT NULL default '0',
  name varchar(128) NOT NULL default ''
);

CREATE INDEX ix_contactgroups_user_id ON contactgroups(user_id, del);


CREATE TABLE contactgroupmembers (
  contactgroup_id integer NOT NULL,
  contact_id integer NOT NULL default '0',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (contactgroup_id, contact_id)
);


-- --------------------------------------------------------

-- 
-- Table structure for table identities
-- 

CREATE TABLE identities (
  identity_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  changed datetime NOT NULL default '0000-00-00 00:00:00',
  del tinyint NOT NULL default '0',
  standard tinyint NOT NULL default '0',
  name varchar(128) NOT NULL default '',
  organization varchar(128) default '',
  email varchar(128) NOT NULL default '',
  "reply-to" varchar(128) NOT NULL default '',
  bcc varchar(128) NOT NULL default '',
  signature text NOT NULL default '',
  html_signature tinyint NOT NULL default '0'
);

CREATE INDEX ix_identities_user_id ON identities(user_id, del);


-- --------------------------------------------------------

-- 
-- Table structure for table users
-- 

CREATE TABLE users (
  user_id integer NOT NULL PRIMARY KEY,
  username varchar(128) NOT NULL default '',
  mail_host varchar(128) NOT NULL default '',
  alias varchar(128) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  last_login datetime DEFAULT NULL,
  language varchar(5),
  preferences text NOT NULL default ''
);

CREATE INDEX ix_users_username ON users(username);
CREATE INDEX ix_users_alias ON users(alias);

-- --------------------------------------------------------

-- 
-- Table structure for table session
-- 

CREATE TABLE session (
  sess_id varchar(40) NOT NULL PRIMARY KEY,
  created datetime NOT NULL default '0000-00-00 00:00:00',
  changed datetime NOT NULL default '0000-00-00 00:00:00',
  ip varchar(40) NOT NULL default '',
  vars text NOT NULL
);

CREATE INDEX ix_session_changed ON session (changed);

-- --------------------------------------------------------

-- 
-- Table structure for table messages
-- 

CREATE TABLE messages (
  message_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  del tinyint NOT NULL default '0',
  cache_key varchar(128) NOT NULL default '',
  created datetime NOT NULL default '0000-00-00 00:00:00',
  idx integer NOT NULL default '0',
  uid integer NOT NULL default '0',
  subject varchar(255) NOT NULL default '',
  "from" varchar(255) NOT NULL default '',
  "to" varchar(255) NOT NULL default '',
  "cc" varchar(255) NOT NULL default '',
  "date" datetime NOT NULL default '0000-00-00 00:00:00',
  size integer NOT NULL default '0',
  headers text NOT NULL,
  structure text
);

CREATE UNIQUE INDEX ix_messages_user_cache_uid ON messages (user_id,cache_key,uid);
CREATE INDEX ix_messages_index ON messages (user_id,cache_key,idx);
CREATE INDEX ix_messages_created ON messages (created);

CREATE TABLE events (
  event_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  start datetime NOT NULL default '1000-01-01 00:00:00',
  end datetime NOT NULL default '1000-01-01 00:00:00',
  summary varchar(255) NOT NULL,
  description text NOT NULL,
  location varchar(255) NOT NULL default '',
  categories varchar(255) NOT NULL default '',
  all_day tinyint(1) NOT NULL default '0',
  CONSTRAINT user_id_fk_events FOREIGN KEY (user_id)
    REFERENCES users(user_id)
);
DROP TABLE accounts;
CREATE TABLE 'accounts' (
   'aid' INTEGER PRIMARY KEY ASC,
   'account_dn' VARCHAR(128) NOT NULL,
   'account_id' VARCHAR(128) NOT NULL,
   'account_pw' VARCHAR(128) NOT NULL,
   'account_host' VARCHAR(128) NOT NULL,
   'preferences' TEXT,
   'user_id' UNSIGNED INTEGER(10) NOT NULL default '0',
   CONSTRAINT 'accounts_ibfk_1' FOREIGN KEY ('user_id') REFERENCES 'users'  
('user_id') ON DELETE
CASCADE ON UPDATE CASCADE
);
CREATE TABLE google_contacts (
  contact_id integer NOT NULL PRIMARY KEY,
  user_id integer NOT NULL default '0',
  changed datetime NOT NULL default '0000-00-00 00:00:00',
  del tinyint NOT NULL default '0',
  name varchar(128) NOT NULL default '',
  email varchar(128) NOT NULL default '',
  firstname varchar(128) NOT NULL default '',
  surname varchar(128) NOT NULL default '',
  vcard text NOT NULL default ''
);

CREATE INDEX ix_google_contacts_user_id ON google_contacts(user_id);

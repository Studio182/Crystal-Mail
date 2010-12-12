-- Crystal Mail DB Structure

--
-- Sequence "user_ids"
-- Name: user_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE user_ids
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "users"
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE users (
    user_id integer DEFAULT nextval('user_ids'::text) PRIMARY KEY,
    username varchar(128) DEFAULT '' NOT NULL,
    mail_host varchar(128) DEFAULT '' NOT NULL,
    alias varchar(128) DEFAULT '' NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL,
    last_login timestamp with time zone DEFAULT NULL,
    "language" varchar(5),
    preferences text DEFAULT ''::text NOT NULL
);

CREATE INDEX users_username_id_idx ON users (username);
CREATE INDEX users_alias_id_idx ON users (alias);

  
--
-- Table "session"
-- Name: session; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE "session" (
    sess_id varchar(40) DEFAULT '' PRIMARY KEY,
    created timestamp with time zone DEFAULT now() NOT NULL,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    ip varchar(41) NOT NULL,
    vars text NOT NULL
);

CREATE INDEX session_changed_idx ON session (changed);


--
-- Sequence "identity_ids"
-- Name: identity_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE identity_ids
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "identities"
-- Name: identities; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE identities (
    identity_id integer DEFAULT nextval('identity_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL
	REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    standard smallint DEFAULT 0 NOT NULL,
    name varchar(128) NOT NULL,
    organization varchar(128),
    email varchar(128) NOT NULL,
    "reply-to" varchar(128),
    bcc varchar(128),
    signature text,
    html_signature integer DEFAULT 0 NOT NULL
);

CREATE INDEX identities_user_id_idx ON identities (user_id, del);


--
-- Sequence "contact_ids"
-- Name: contact_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE contact_ids
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "contacts"
-- Name: contacts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE contacts (
    contact_id integer DEFAULT nextval('contact_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL
	REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    name varchar(128) DEFAULT '' NOT NULL,
    email varchar(128) DEFAULT '' NOT NULL,
    firstname varchar(128) DEFAULT '' NOT NULL,
    surname varchar(128) DEFAULT '' NOT NULL,
    vcard text
);

CREATE INDEX contacts_user_id_idx ON contacts (user_id, email);

--
-- Sequence "contactgroups_ids"
-- Name: contactgroups_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE contactgroups_ids
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "contactgroups"
-- Name: contactgroups; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE contactgroups (
    contactgroup_id integer DEFAULT nextval('contactgroups_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL
        REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint NOT NULL DEFAULT 0,
    name varchar(128) NOT NULL DEFAULT ''
);

CREATE INDEX contactgroups_user_id_idx ON contactgroups (user_id, del);

--
-- Table "contactgroupmembers"
-- Name: contactgroupmembers; Type: TABLE; Schema: public; Owner: postgres
--
					    
CREATE TABLE contactgroupmembers (
    contactgroup_id integer NOT NULL
        REFERENCES contactgroups(contactgroup_id) ON DELETE CASCADE ON UPDATE CASCADE,
    contact_id integer NOT NULL
        REFERENCES contacts(contact_id) ON DELETE CASCADE ON UPDATE CASCADE,
    created timestamp with time zone DEFAULT now() NOT NULL,
    PRIMARY KEY (contactgroup_id, contact_id)
);

--
-- Sequence "cache_ids"
-- Name: cache_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE cache_ids
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "cache"
-- Name: cache; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE "cache" (
    cache_id integer DEFAULT nextval('cache_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL
	REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    cache_key varchar(128) DEFAULT '' NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL,
    data text NOT NULL
);

CREATE INDEX cache_user_id_idx ON "cache" (user_id, cache_key);
CREATE INDEX cache_created_idx ON "cache" (created);

--
-- Sequence "message_ids"
-- Name: message_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE message_ids
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "messages"
-- Name: messages; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE messages (
    message_id integer DEFAULT nextval('message_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL
	REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    del smallint DEFAULT 0 NOT NULL,
    cache_key varchar(128) DEFAULT '' NOT NULL,
    created timestamp with time zone DEFAULT now() NOT NULL,
    idx integer DEFAULT 0 NOT NULL,
    uid integer DEFAULT 0 NOT NULL,
    subject varchar(128) DEFAULT '' NOT NULL,
    "from" varchar(128) DEFAULT '' NOT NULL,
    "to" varchar(128) DEFAULT '' NOT NULL,
    cc varchar(128) DEFAULT '' NOT NULL,
    date timestamp with time zone NOT NULL,
    size integer DEFAULT 0 NOT NULL,
    headers text NOT NULL,
    structure text
);

ALTER TABLE messages ADD UNIQUE (user_id, cache_key, uid);
CREATE INDEX messages_index_idx ON messages (user_id, cache_key, idx);
CREATE INDEX messages_created_idx ON messages (created);

CREATE SEQUENCE event_ids
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

CREATE TABLE events (
    event_id integer DEFAULT nextval('event_ids'::regclass) NOT NULL,
    user_id integer NOT NULL,
    "start" timestamp without time zone DEFAULT now() NOT NULL,
    "end" timestamp without time zone DEFAULT now() NOT NULL,
    "summary" character varying(255) NOT NULL,
    "description" text NOT NULL,
    "location" character varying(255) NOT NULL,
    "categories" character varying(255) NOT NULL,
    "all_day" smallint NOT NULL DEFAULT 0
);

CREATE INDEX events_event_id_idx ON events USING btree (event_id);


--
-- Constraints Table `events`
--
ALTER TABLE ONLY events
    ADD CONSTRAINT events_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;
DROP SEQUENCE accounts_id_seq;
CREATE SEQUENCE accounts_id_seq;
DROP TABLE accounts;
CREATE TABLE accounts (
  aid integer DEFAULT nextval('accounts_id_seq'::text),
  account_dn varchar(128) NOT NULL,  
  account_id varchar(128) NOT NULL,
  account_pw varchar(128) NOT NULL,
  account_host varchar(128) NOT NULL,
  preferences text,
  user_id integer NOT NULL default '0',
  PRIMARY KEY (aid)
);

CREATE INDEX user_id_fk_accounts ON accounts (user_id);

ALTER TABLE accounts
  ADD CONSTRAINT accounts_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Sequence "collected_contact_ids"
-- Name: collected_contact_ids; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE collected_contact_ids
    START WITH 1
    INCREMENT BY 1
    NO MAXVALUE
    NO MINVALUE
    CACHE 1;

--
-- Table "google_contacts"
-- Name: google_contacts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE google_contacts (
    contact_id integer DEFAULT nextval('collected_contact_ids'::text) PRIMARY KEY,
    user_id integer NOT NULL REFERENCES users (user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    changed timestamp with time zone DEFAULT now() NOT NULL,
    del smallint DEFAULT 0 NOT NULL,
    name character varying(128) DEFAULT ''::character varying NOT NULL,
    email character varying(128) DEFAULT ''::character varying NOT NULL,
    firstname character varying(128) DEFAULT ''::character varying NOT NULL,
    surname character varying(128) DEFAULT ''::character varying NOT NULL,
    vcard text
);

CREATE INDEX google_contacts_user_id_idx ON google_contacts (user_id);


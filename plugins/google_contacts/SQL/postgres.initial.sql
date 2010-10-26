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

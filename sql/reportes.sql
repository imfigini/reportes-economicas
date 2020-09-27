--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: prueba; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE prueba (
    id integer NOT NULL,
    descripcion character varying(200)
);


ALTER TABLE public.prueba OWNER TO postgres;

--
-- Name: prueba_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE prueba_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.prueba_id_seq OWNER TO postgres;

--
-- Name: prueba_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE prueba_id_seq OWNED BY prueba.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY prueba ALTER COLUMN id SET DEFAULT nextval('prueba_id_seq'::regclass);


--
-- Data for Name: prueba; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY prueba (id, descripcion) FROM stdin;
1	uno
2	dos
3	tres
4	cuatro
\.


--
-- Name: prueba_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('prueba_id_seq', 4, true);


--
-- Name: pk_prueba; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY prueba
    ADD CONSTRAINT pk_prueba PRIMARY KEY (id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--


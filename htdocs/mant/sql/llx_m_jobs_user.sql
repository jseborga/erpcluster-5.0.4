CREATE TABLE llx_m_jobs_user (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_jobs integer NOT NULL,
  fk_user integer NOT NULL,
  fk_level integer DEFAULT NULL,
  detail text,
  fk_user_create integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL DEFAULT '0',
  datec date DEFAULT NULL,
  datem date DEFAULT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

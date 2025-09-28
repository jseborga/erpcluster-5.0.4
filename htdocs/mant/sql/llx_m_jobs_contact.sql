CREATE TABLE llx_m_jobs_contact (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_jobs integer NOT NULL,
  fk_contact integer NOT NULL,
  fk_charge integer DEFAULT NULL,
  detail text,
  fk_user_create integer NOT NULL DEFAULT '0',
  fK_user_mod integer NOT NULL DEFAULT '0',
  datec date DEFAULT NULL,
  datem date DEFAULT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

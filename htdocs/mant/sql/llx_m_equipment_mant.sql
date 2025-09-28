CREATE TABLE llx_m_equipment_mant (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_equipment integer NOT NULL,
  fk_jobs integer NOT NULL DEFAULT '0',
  dater date NOT NULL,
  type varchar(1) DEFAULT 'C' NOT NULL,
  accountant double NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint(4) NOT NULL
) ENGINE=InnoDB;
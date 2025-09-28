CREATE TABLE llx_m_location (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_property integer NOT NULL,
  detail varchar(255) NOT NULL,
  safety tinyint DEFAULT '0' NULL,
  fk_user_create integer DEFAULT '0' NOT NULL
  fk_user_mod integer DEFAULT '0' NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint DEFAULT '1' NOT NULL
) ENGINE=InnoDB;


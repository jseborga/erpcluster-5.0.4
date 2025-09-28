CREATE TABLE llx_m_property_user (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_property integer NOT NULL,
  fk_user integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  active tinyint NOT NULL DEFAULT '0',
  tms timestamp,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
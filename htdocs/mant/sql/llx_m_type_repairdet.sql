CREATE TABLE llx_m_type_repairdet (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_type_repair integer NOT NULL,
  fk_parent integer NOT NULL DEFAULT '0',
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
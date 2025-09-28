CREATE TABLE llx_m_equipment_historial (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_equipment integer NOT NULL,
  ref_ext varchar(30) DEFAULT NULL,
  accountant double NOT NULL,
  accountant_last double NOT NULL DEFAULT '0',
  description text,
  pc_ip varchar(30) NOT NULL,
  origin varchar(150) DEFAULT NULL,
  originid integer DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
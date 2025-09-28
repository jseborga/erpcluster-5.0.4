CREATE TABLE llx_c_type_item (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(50) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_asset_historial (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_asset integer NOT NULL,
  ref_ext varchar(30) DEFAULT NULL,
  been varchar(30) NOT NULL,
  description text,
  pc_ip varchar(30) NOT NULL,
  origin varchar(150) DEFAULT NULL,
  originid integer DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL
) ENGINE=InnoDB;
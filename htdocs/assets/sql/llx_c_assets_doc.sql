CREATE TABLE llx_c_assets_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(120) NOT NULL,
  active tinyint NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

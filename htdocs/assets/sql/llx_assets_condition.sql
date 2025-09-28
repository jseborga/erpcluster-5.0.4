CREATE TABLE llx_assets_condition (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_asset integer NOT NULL,
  ref varchar(30) NOT NULL,
  fk_user integer NOT NULL,
  dater date NOT NULL,
  been varchar(30) NOT NULL,
  description text,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint(4) NOT NULL
) ENGINE=InnoDB;
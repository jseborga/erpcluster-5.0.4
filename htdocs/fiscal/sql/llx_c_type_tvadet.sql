CREATE TABLE llx_c_type_tvadet (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_type_tva integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  type tinyint(1) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
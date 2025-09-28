CREATE TABLE llx_tva_add (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_tva integer NOT NULL,
  fk_type_tva integer NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
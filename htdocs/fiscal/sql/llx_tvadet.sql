CREATE TABLE llx_tvadet (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_tva integer NOT NULL,
  fk_typetvadet integer NOT NULL,
  amount double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint(4) NOT NULL
) ENGINE=InnoDB;
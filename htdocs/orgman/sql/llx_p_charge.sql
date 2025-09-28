CREATE TABLE llx_p_charge (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(150),
  detail text,
  amount double DEFAULT '0' NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
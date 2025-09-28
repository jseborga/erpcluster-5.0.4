CREATE TABLE llx_city (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_country integer NOT NULL,
  fk_departement integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  description text,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1',
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
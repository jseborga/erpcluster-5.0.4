CREATE TABLE llx_c_region_geographic (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_pays integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(150) NOT NULL,
  fk_parent integer DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
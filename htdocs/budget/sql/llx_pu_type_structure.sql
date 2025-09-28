CREATE TABLE llx_pu_type_structure (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(200) NOT NULL,
  active tinyint DEFAULT '1' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint DEFAULT '0' NOT NULL
) ENGINE=InnoDB;
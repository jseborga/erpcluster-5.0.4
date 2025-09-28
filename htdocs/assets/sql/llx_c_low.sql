CREATE TABLE llx_c_low (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) COLLATE utf8_bin NOT NULL,
  label varchar(255) COLLATE utf8_bin NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

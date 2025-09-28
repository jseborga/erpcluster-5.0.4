CREATE TABLE llx_member_assistance (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_member integer NOT NULL,
  dater date NOT NULL,
  type_marking varchar(30) COLLATE utf8_bin NOT NULL,
  aditional_time tinyint NOT NULL,
  backwardness tinyint NOT NULL,
  lack tinyint NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint DEFAULT '0'
) ENGINE=InnoDB;
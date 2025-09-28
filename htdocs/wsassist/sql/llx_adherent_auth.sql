CREATE TABLE llx_adherent_auth (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_adherent integer NOT NULL,
  fk_property integer NOT NULL,
  code_mobile varchar(30) COLLATE utf8_bin NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_incidents_res (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_incident integer NOT NULL,
  type varchar(30) COLLATE utf8_bin NOT NULL,
  group_det tinyint DEFAULT '0' NOT NULL,
  incident double(24,8) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;


CREATE TABLE llx_licences_log (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_licence integer NOT NULL,
  description text,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec datetime NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

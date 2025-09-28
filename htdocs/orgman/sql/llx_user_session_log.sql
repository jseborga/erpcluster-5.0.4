CREATE TABLE llx_user_session_log (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_user integer NOT NULL,
  datelog date NOT NULL,
  tries integer DEFAULT '0' NOT NULL,
  datec date NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
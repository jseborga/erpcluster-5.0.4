CREATE TABLE llx_user_log (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_user integer NOT NULL,
  description text,
  status_product tinyint NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec datetime NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
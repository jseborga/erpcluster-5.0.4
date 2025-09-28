CREATE TABLE llx_request_cash_log (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_request_cash integer NOT NULL,
  description text,
  amount double DEFAULT '0' NULL,
  status_cash tinyint DEFAULT '0' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec datetime NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

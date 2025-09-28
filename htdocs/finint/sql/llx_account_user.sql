CREATE TABLE llx_account_user (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_account integer NOT NULL,
  fk_user integer NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer DEFAULT '0',
  date_create date NOT NULL,
  tms timestamp,
  status tinyint NOT NULL
) ENGINE=InnoDB;

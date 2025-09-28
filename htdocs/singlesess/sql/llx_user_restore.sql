CREATE TABLE llx_user_restore (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_user integer NOT NULL,
  coderest varchar(20) NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

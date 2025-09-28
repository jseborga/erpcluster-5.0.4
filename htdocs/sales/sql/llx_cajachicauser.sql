CREATE TABLE llx_cajachicauser (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_cajachica integer NOT NULL,
  fk_user integer NOT NULL
) ENGINE=InnoDB;

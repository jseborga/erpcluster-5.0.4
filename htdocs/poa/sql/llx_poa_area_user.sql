CREATE TABLE llx_poa_area_user (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_area integer NOT NULL,
  fk_user integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  active tinyint NOT NULL DEFAULT '0',
  privilege tinyint NULL DEFAULT '0'
) ENGINE=InnoDB;

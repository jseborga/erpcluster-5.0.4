CREATE TABLE llx_c_type_tva (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_pays integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(60) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
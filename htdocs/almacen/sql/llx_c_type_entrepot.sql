CREATE TABLE llx_c_type_entrepot (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(12) NOT NULL,
  label varchar(60) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
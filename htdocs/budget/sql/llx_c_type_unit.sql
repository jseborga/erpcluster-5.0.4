CREATE TABLE llx_c_type_unit (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(12) NOT NULL,
  libelle varchar(30) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

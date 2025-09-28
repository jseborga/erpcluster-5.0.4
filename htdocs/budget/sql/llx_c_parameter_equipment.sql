CREATE TABLE llx_c_parameter_equipment (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(2) NOT NULL,
  label varchar(50) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
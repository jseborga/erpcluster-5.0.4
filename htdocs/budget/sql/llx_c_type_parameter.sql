CREATE TABLE llx_c_type_parameter (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(150) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
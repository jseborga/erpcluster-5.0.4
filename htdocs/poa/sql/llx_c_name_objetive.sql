CREATE TABLE llx_c_name_objetive (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;
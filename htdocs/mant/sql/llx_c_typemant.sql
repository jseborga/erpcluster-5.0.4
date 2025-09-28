CREATE TABLE llx_c_typemant (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

CREATE TABLE llx_c_working_class (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

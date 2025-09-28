CREATE TABLE llx_c_type_time_limit (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

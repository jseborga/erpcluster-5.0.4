CREATE TABLE llx_c_deductions (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  sequence tinyint DEFAULT '0' NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

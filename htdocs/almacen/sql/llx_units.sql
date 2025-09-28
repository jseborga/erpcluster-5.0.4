CREATE TABLE llx_units (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  ref varchar(6) NOT NULL,
  description varchar(150) NOT NULL,
  active tinyint NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE llx_c_units (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(3) DEFAULT NULL,
  label varchar(50) DEFAULT NULL,
  short_label varchar(5) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

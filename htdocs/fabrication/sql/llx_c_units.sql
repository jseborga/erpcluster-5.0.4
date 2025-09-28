CREATE TABLE llx_c_units (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  code varchar(3) DEFAULT NULL,
  label varchar(50) DEFAULT NULL,
  short_label varchar(5) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

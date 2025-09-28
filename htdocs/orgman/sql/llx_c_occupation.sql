CREATE TABLE llx_c_occupation (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(100) NOT NULL,
  detail text,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

CREATE TABLE llx_c_assets_method (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
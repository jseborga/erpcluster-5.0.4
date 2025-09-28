CREATE TABLE llx_c_assets_been (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(40) NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;

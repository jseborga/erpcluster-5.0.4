CREATE TABLE llx_c_tables (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  table varchar(2) NOT NULL,
  code tinyint NOT NULL,
  label varchar(200) NOT NULL,
  type varchar(50) NOT NULL,
  range_ini integer NOT NULL,
  range_fin integer NOT NULL,
  active tinyint NOT NULL DEFAULT "1"
) ENGINE=InnoDB;

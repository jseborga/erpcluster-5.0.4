CREATE TABLE llx_c_orgfin (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(100) NOT NULL,
  detail varchar(300) NOT NULL,
  ref_ext varchar(50) NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

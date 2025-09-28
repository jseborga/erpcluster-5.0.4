CREATE TABLE llx_c_clasfin (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  detail text NULL,
  fk_father integer(11) NULL,
  ref_ext varchar(50) NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
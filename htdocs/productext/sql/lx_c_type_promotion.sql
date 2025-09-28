CREATE TABLE llx_c_type_promotion (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(150) NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
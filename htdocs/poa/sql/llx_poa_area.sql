CREATE TABLE llx_poa_area (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_father integer DEFAULT NULL,
  code_actor varchar(30) DEFAULT '' NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  pos tinyint NOT NULL DEFAULT '0',
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

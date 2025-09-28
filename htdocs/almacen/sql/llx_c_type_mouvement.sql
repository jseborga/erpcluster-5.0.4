CREATE TABLE llx_c_type_mouvement (
  rowid INTEGER PRIMARY KEY AUTO_INCREMENT,
  entity integer NOT NULL DEFAULT '1',
  code varchar(30) NOT NULL,
  label varchar(60) NOT NULL,
  type varchar(1) NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

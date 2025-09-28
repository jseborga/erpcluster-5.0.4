CREATE TABLE llx_c_typeprocedure (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  sigla varchar(30) NULL,
  landmark tinyint NOT NULL DEFAULT '0',
  colour varchar(6) NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

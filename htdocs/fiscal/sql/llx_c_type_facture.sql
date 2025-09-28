CREATE TABLE llx_c_type_facture (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_pays integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(60) DEFAULT NULL,
  detail varchar(255) DEFAULT NULL,
  type_fact tinyint NOT NULL DEFAULT '0',
  type_value tinyint NOT NULL DEFAULT '0',
  retention tinyint NOT NULL DEFAULT '0',
  nit_required tinyint NULL DEFAULT '0',
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

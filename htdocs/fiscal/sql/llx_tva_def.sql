CREATE TABLE llx_tva_def (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_pays integer NOT NULL,
  code_facture varchar(12) NOT NULL,
  code_tva varchar(12) NOT NULL,
  taux double NOT NULL,
  register_mode tinyint NOT NULL DEFAULT '0',
  note varchar(128) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1',
  accountancy_code varchar(32) DEFAULT NULL,
  against_account varchar(32) NULL
) ENGINE=InnoDB;
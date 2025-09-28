CREATE TABLE llx_c_tva_local (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_pays integer NOT NULL,
  code varchar(10) NOT NULL,
  taux double NOT NULL,
  localtax1 varchar(20) DEFAULT NULL,
  localtax2 varchar(20) DEFAULT NULL,
  recuperableonly integer NOT NULL DEFAULT '0',
  note varchar(128) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1',
  accountancy_code_sell varchar(32) DEFAULT NULL,
  accountancy_code_buy varchar(32) DEFAULT NULL
) ENGINE=InnoDB;
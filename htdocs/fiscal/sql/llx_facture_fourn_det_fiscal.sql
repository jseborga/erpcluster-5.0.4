CREATE TABLE llx_facture_fourn_det_fiscal (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_facture_fourn_det integer NOT NULL,
  code_tva varchar(12) NOT NULL,
  tva_tx double NOT NULL DEFAULT '0',
  total_tva double NOT NULL DEFAULT '0',
  total_ht double NOT NULL DEFAULT '0',
  total_ttc double NOT NULL DEFAULT '0',
  amount_base double NOT NULL DEFAULT '0',
  amount_ice double NULL DEFAULT '0',
  discount double NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

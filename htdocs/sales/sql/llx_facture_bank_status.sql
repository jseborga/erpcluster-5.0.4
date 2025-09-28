CREATE TABLE llx_facture_bank_status (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_facture integer NOT NULL,
  fk_bank_status integer NOT NULL,
  fk_user_sale integer DEFAULT '0' NULL,
  detail text NULL,
  exchange double(24,5) DEFAULT '0' NULL,
  label text NULL,
  fk_facture_rel integer DEFAULT '0' NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;
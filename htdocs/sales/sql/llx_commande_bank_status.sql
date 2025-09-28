CREATE TABLE llx_commande_bank_status (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_commande integer NOT NULL,
  fk_bank_status integer NOT NULL,
  fk_bank integer NOT NULL,
  detail text,
  exchange double(24,5) DEFAULT '0' NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

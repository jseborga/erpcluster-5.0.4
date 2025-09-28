CREATE TABLE llx_projet_payment (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_projet integer NOT NULL,
  ref varchar(30) NOT NULL,
  date_payment date NOT NULL,
  date_request date NOT NULL,
  amount double(24,5) DEFAULT '0' NULL,
  document text NULL,
  detail text NULL,
  fk_facture integer DEFAULT '0' NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

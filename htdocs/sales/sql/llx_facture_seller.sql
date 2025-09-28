CREATE TABLE llx_facture_seller (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_facture integer NOT NULL,
  fk_seller integer NOT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

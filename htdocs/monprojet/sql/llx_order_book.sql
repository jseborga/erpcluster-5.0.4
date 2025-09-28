CREATE TABLE llx_order_book (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_parent integer DEFAULT '0' NOT NULL,
  fk_projet integer NOT NULL,
  fk_contrat integer NOT NULL,
  ref varchar(30) NOT NULL,
  date_order datetime NOT NULL,
  detail text NOT NULL,
  document text NULL,
  fk_user_create integer NOT NULL,
  fk_user_validate integer DEFAULT '0',
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
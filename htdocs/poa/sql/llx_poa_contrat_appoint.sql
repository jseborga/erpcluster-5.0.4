CREATE TABLE llx_poa_contrat_appoint (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_contrat integer NOT NULL,
  fk_user integer NOT NULL,
  fk_user_replace integer DEFAULT '0',
  date_appoint date NOT NULL,
  code_appoint varchar(30) NOT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;


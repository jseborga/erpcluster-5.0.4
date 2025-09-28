CREATE TABLE llx_poa_workflow (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_prev integer NOT NULL,
  deadlines integer DEFAULT '0' NOT NULL,
  contrat varchar(1) NOT NULL DEFAULT 'N',
  date_workflow datetime NOT NULL,
  doclink text NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint(4) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE llx_poa_process_contrat (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_process integer NOT NULL,
  fk_contrat integer DEFAULT NULL,
  date_create date NOT NULL,
  date_order_proceed date NULL,
  date_provisional date NULL,
  date_final date NULL,
  date_nonconformity date NULL,
  nonconformity tinyint NULL,
  motif text NULL,
  fk_user_create integer NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

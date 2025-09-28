CREATE TABLE llx_poa_prev_process (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_prev integer NOT NULL,
  fk_poa_process integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  fk_user_create integer NOT NULL,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

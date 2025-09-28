CREATE TABLE llx_poa_reformulated_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_reformulated integer NOT NULL,
  fk_structure integer NOT NULL,
  fk_poa_poa integer NOT NULL,
  partida varchar(10) NOT NULL,
  amount double NOT NULL,
  reform varchar(15) NULL,
  date_create date NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

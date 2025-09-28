CREATE TABLE llx_contrat_deduction (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_contrat integer NOT NULL,
  code varchar(30) NOT NULL,
  amount double(24,5) NOT NULL,
  percentage double(5,2) NOT NULL DEFAULT '0.00',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
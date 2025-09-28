CREATE TABLE llx_projet_payment_deduction (
  rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
  fk_projet_payment integer NOT NULL,
  code varchar(30) NOT NULL,
  amount double(24,5) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

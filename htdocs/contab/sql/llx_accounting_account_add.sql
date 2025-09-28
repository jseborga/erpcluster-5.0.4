CREATE TABLE llx_accounting_account_add (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_accounting_account integer NOT NULL,
  cta_class varchar(30) NULL,
  cta_normal varchar(30) NULL,
  level tinyint NULL,
  fk_user_create integer DEFAULT NULL,
  fk_user_mod integer DEFAULT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;
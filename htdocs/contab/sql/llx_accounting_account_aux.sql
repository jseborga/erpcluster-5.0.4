CREATE TABLE llx_accounting_account_aux (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_accounting_account integer NOT NULL,
  ref varchar(30) NOT NULL,
  code_father varchar(30) DEFAULT NULL,
  label varchar(50) NOT NULL,
  fk_soc integer DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
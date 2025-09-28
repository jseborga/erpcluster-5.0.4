CREATE TABLE llx_entrepot_bank_soc (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  numero_ip varchar(15) NULL,
  fk_user integer DEFAULT 0 NULL,
  fk_entrepotid integer NOT NULL,
  fk_socid integer DEFAULT 0 NULL,
  fk_cajaid integer DEFAULT 0 NULL,
  fk_bankid integer DEFAULT 0 NULL,
  fk_banktcid integer DEFAULT 0 NULL,
  fk_subsidiaryid integer DEFAULT 0 NULL,
  series varchar(4) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

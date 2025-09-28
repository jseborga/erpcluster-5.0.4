CREATE TABLE llx_expeditiondet_add (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_expeditiondet integer NOT NULL,
  fk_facture integer DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create datetime NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
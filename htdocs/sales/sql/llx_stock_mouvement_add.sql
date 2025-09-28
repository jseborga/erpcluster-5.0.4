CREATE TABLE llx_stock_mouvement_add (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_stock_mouvement integer NOT NULL,
  fk_facture integer DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create datetime NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
CREATE TABLE llx_commande_fournisseur_dispatch_add (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_commande_fournisseur_dispatch integer NOT NULL,
  fk_stock_mouvement_doc integer NOT NULL,
  ref varchar(30) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
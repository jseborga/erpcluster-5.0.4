ALTER TABLE llx_commande_sale ADD UNIQUE uk_unique (fk_commande);
ALTER TABLE llx_commande_sale ADD fk_entrepot_end INT NULL AFTER fk_entrepot;
ALTER TABLE llx_commande_sale ADD date_livraison DATETIME NULL AFTER fk_entrepot_end;
ALTER TABLE llx_commande_sale ADD amount_advance DOUBLE(20,5) NOT NULL DEFAULT '0' AFTER date_livraison;
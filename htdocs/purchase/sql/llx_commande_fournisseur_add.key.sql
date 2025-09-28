ALTER TABLE llx_commande_fournisseur_add ADD UNIQUE uk_unique (fk_facture_fournisseur);

ALTER TABLE llx_commande_fournisseur_add ADD object VARCHAR(255) NULL AFTER fk_commande_fournisseur;
ALTER TABLE llx_commande_fournisseur_add ADD fk_object INTEGER NULL DEFAULT '0' AFTER object;
ALTER TABLE llx_commande_fournisseur_add ADD fk_departament INTEGER NULL DEFAULT '0' AFTER fk_object;
ALTER TABLE llx_commande_fournisseur_add ADD fk_poa_prev INTEGER NULL DEFAULT '0' AFTER fk_departament;

ALTER TABLE llx_commande_fournisseur_add ADD ref_contrat VARCHAR(40) NULL AFTER fk_departament;
ALTER TABLE llx_commande_fournisseur_add ADD term INTEGER NULL AFTER ref_contrat;
ALTER TABLE llx_commande_fournisseur_add ADD ref_term TINYINT NULL AFTER term;
ALTER TABLE llx_commande_fournisseur_add ADD type VARCHAR(2) NULL AFTER ref_term;
ALTER TABLE llx_commande_fournisseur_add ADD advance TINYINT NULL AFTER type;
ALTER TABLE llx_commande_fournisseur_add ADD order_proceed TINYINT NULL AFTER advance;
ALTER TABLE llx_commande_fournisseur_add ADD designation_fiscal TINYINT NULL AFTER order_proceed;
ALTER TABLE llx_commande_fournisseur_add ADD designation_supervisor TINYINT NULL AFTER designation_fiscal;
ALTER TABLE llx_commande_fournisseur_add ADD date_ini DATE NULL AFTER designation_supervisor;
ALTER TABLE llx_commande_fournisseur_add ADD date_fin DATE NULL AFTER date_ini;
ALTER TABLE llx_commande_fournisseur_add ADD date_order_proced DATE NULL AFTER date_fin;
ALTER TABLE llx_commande_fournisseur_add ADD delivery_place TEXT NULL DEFAULT NULL AFTER order_proceed;
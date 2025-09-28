ALTER TABLE llx_stock_mouvement_add ADD UNIQUE uk_unique (fk_stock_mouvement);

ALTER TABLE llx_stock_mouvement_add ADD fk_stock_mouvement INTEGER NULL DEFAULT '0' AFTER rowid;
ALTER TABLE llx_stock_mouvement_add ADD fk_user_mod INTEGER NULL DEFAULT '0' AFTER fk_user_create;
ALTER TABLE llx_stock_mouvement_add ADD balance_peps DOUBLE NULL DEFAULT '0' AFTER fk_user_mod;
ALTER TABLE llx_stock_mouvement_add ADD balance_ueps DOUBLE NULL DEFAULT '0' AFTER balance_peps;
ALTER TABLE llx_stock_mouvement_add ADD value_peps DOUBLE NULL DEFAULT '0' AFTER balance_ueps;
ALTER TABLE llx_stock_mouvement_add ADD value_ueps DOUBLE NULL DEFAULT '0' AFTER value_peps;
ALTER TABLE llx_stock_mouvement_add ADD fk_parent_line INTEGER NULL DEFAULT '0' AFTER fk_user_mod;
ALTER TABLE llx_stock_mouvement_add ADD fk_stock_mouvement_doc INTEGER DEFAULT '0' NOT NULL AFTER fk_stock_mouvement;
ALTER TABLE llx_stock_mouvement_add ADD qty DOUBLE NULL DEFAULT '0' AFTER fk_parent_line;
ALTER TABLE llx_stock_mouvement_add ADD period_year MEDIUMINT NOT NULL DEFAULT '0' AFTER fk_stock_mouvement;
ALTER TABLE llx_stock_mouvement_add ADD month_year TINYINT NOT NULL DEFAULT '1' AFTER period_year;

ALTER TABLE llx_stock_mouvement_add ADD value_peps_adq DOUBLE NULL DEFAULT '0' AFTER value_ueps;
ALTER TABLE llx_stock_mouvement_add ADD value_ueps_adq DOUBLE NULL DEFAULT '0' AFTER value_peps_adq;
ALTER TABLE llx_stock_mouvement_add ADD fk_facturedet INT NULL DEFAULT NULL AFTER fk_facture;
ALTER TABLE llx_stock_mouvement_add ADD process_type TINYINT NOT NULL DEFAULT '0' AFTER value_ueps_adq;
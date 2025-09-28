ALTER TABLE llx_stock_mouvement_temp ADD UNIQUE uk_unique (entity, ref, fk_product, fk_entrepot);
ALTER TABLE llx_stock_mouvement_temp ADD fk_type_mov INTEGER NOT NULL DEFAULT '0' AFTER fk_entrepot;

ALTER TABLE llx_stock_mouvement_temp ADD INDEX idx_typemov (fk_type_mov);
ALTER TABLE llx_stock_mouvement_temp ADD balance_peps DOUBLE NULL DEFAULT '0' AFTER price;
ALTER TABLE llx_stock_mouvement_temp ADD balance_ueps DOUBLE NULL DEFAULT '0' AFTER balance_peps;
ALTER TABLE llx_stock_mouvement_temp ADD price_peps DOUBLE NULL DEFAULT '0' AFTER balance_ueps;
ALTER TABLE llx_stock_mouvement_temp ADD price_ueps DOUBLE NULL DEFAULT '0' AFTER price_peps;


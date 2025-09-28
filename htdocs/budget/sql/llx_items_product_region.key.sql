ALTER TABLE llx_items_product_region ADD UNIQUE KEY uk_unique (fk_item_product,fk_region,fk_sector);
ALTER TABLE llx_items_product_region ADD cost_direct DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER amount;

ALTER TABLE llx_items_product_region ADD fk_origin INTEGER NULL DEFAULT '0' AFTER fk_sector;
ALTER TABLE llx_items_product_region ADD percent_origin DOUBLE(7,3) NULL DEFAULT '100' AFTER fk_origin;
ALTER TABLE llx_items_product_region ADD hour_production DOUBLE(24,8) NULL DEFAULT NULL AFTER performance;
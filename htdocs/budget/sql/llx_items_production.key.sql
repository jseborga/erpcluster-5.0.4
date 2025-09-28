ALTER TABLE llx_items_production ADD UNIQUE KEY uk_unique (fk_item,fk_variable,fk_items_product);

ALTER TABLE llx_items_production ADD fk_region INTEGER DEFAULT '0' NOT NULL AFTER fk_items_product;
ALTER TABLE llx_items_production ADD fk_sector INTEGER DEFAULT '0' NOT NULL AFTER fk_region;

ALTER TABLE llx_items_production DROP INDEX uk_unique;
ALTER TABLE llx_items_production ADD UNIQUE uk_unique (fk_item, fk_variable, fk_items_product, fk_region, fk_sector);

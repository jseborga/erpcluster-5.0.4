ALTER TABLE llx_items_region ADD UNIQUE uk_unique(fk_item,fk_region,fk_sector);

ALTER TABLE llx_items_region ADD active TINYINT NOT NULL DEFAULT '1' AFTER amount;
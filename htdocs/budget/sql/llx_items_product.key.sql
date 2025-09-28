ALTER TABLE llx_items_product ADD UNIQUE uk_unique (fk_item, ref);
ALTER TABLE llx_items_product ADD fk_unit INT NOT NULL DEFAULT '0' AFTER fk_product;
ALTER TABLE llx_items_product DROP INDEX uk_unique_label;
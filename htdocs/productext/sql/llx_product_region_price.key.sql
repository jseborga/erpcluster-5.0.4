ALTER TABLE llx_product_region_price CHANGE fk_altitude fk_soc INTEGER NOT NULL;
ALTER TABLE llx_product_region_price ADD fk_supplier_proposal_det INTEGER NULL DEFAULT NULL AFTER fk_soc;
ALTER TABLE llx_product_region_price ADD date_create DATETIME NULL DEFAULT NULL AFTER fk_supplier_proposal_det;
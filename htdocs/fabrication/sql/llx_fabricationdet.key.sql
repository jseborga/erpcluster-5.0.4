ALTER TABLE llx_fabricationdet ADD UNIQUE INDEX uk_fkfabrication_fkproduct (fk_fabrication,fk_product);
ALTER TABLE llx_fabricationdet ADD price DOUBLE NULL AFTER qty_second;
ALTER TABLE llx_fabricationdet ADD fk_commandedet INTEGER NULL DEFAULT '0' AFTER fk_product;
ALTER TABLE llx_fabricationdet DROP INDEX uk_fkfabrication_fkproduct;
ALTER TABLE llx_fabricationdet ADD UNIQUE uk_unique (fk_fabrication, fk_product, fk_commandedet);
ALTER TABLE llx_fabricationdet CHANGE date_end date_end DATETIME NULL DEFAULT NULL;
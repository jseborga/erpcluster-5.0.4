ALTER TABLE llx_product_add ADD UNIQUE uk_unique (fk_product);


ALTER TABLE llx_product_add ADD fk_unit_ext INTEGER NULL DEFAULT '0' AFTER sel_iva;
ALTER TABLE llx_product_add ADD fk_product_type INTEGER NULL DEFAULT '0' AFTER fk_unit_ext;
ALTER TABLE llx_product_add ADD quant_convert DOUBLE NULL DEFAULT NULL AFTER fk_product_type;
ALTER TABLE llx_product_add ADD quant_disassembly DOUBLE NULL DEFAULT NULL AFTER quant_convert;
ALTER TABLE llx_product_add ADD quant_material DOUBLE NULL DEFAULT NULL AFTER quant_disassembly;
ALTER TABLE llx_product_add ADD price_std DOUBLE NULL DEFAULT '0' AFTER quant_material;

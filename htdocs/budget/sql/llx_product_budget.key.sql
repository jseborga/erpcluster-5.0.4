ALTER TABLE llx_product_budget DROP INDEX uk_budget;
ALTER TABLE llx_product_budget ADD UNIQUE uk_budget (fk_budget, ref, code_structure);
ALTER TABLE llx_product_budget ADD UNIQUE uk_unique_label ( fk_budget, code_structure, label);
ALTER TABLE llx_product_budget ADD percent_prod DOUBLE NOT NULL DEFAULT '100' AFTER quant;
ALTER TABLE llx_product_budget ADD amount_noprod DOUBLE NOT NULL DEFAULT '0' AFTER percent_prod;
ALTER TABLE llx_product_budget ADD group_structure VARCHAR(2) NULL DEFAULT NULL AFTER code_structure;
ALTER TABLE llx_product_budget ADD formula VARCHAR(100) NULL DEFAULT NULL AFTER group_structure;
ALTER TABLE llx_product_budget ADD units INTEGER NULL AFTER formula;
ALTER TABLE llx_product_budget ADD commander TINYINT NULL DEFAULT '0' AFTER units;
ALTER TABLE llx_product_budget ADD price_productive DOUBLE NULL DEFAULT '0' AFTER commander;
ALTER TABLE llx_product_budget ADD price_improductive DOUBLE NULL DEFAULT '0' AFTER price_productive;
ALTER TABLE llx_product_budget ADD active TINYINT NULL DEFAULT '1' AFTER price_improductive;
ALTER TABLE llx_product_budget ADD fk_object INTEGER NULL DEFAULT '0' AFTER active;
ALTER TABLE llx_product_budget ADD performance DOUBLE(24,8) NULL DEFAULT '0' AFTER commander;

ALTER TABLE llx_product_budget ADD fk_origin INTEGER NULL DEFAULT '1' AFTER fk_object;
ALTER TABLE llx_product_budget ADD percent_origin DOUBLE(7,3) NULL DEFAULT '100' AFTER fk_origin;

ALTER TABLE llx_product_budget ADD CONSTRAINT fk_productbudget_cunits_fkunit_rowid FOREIGN KEY (fk_unit) REFERENCES llx_c_units(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_product_budget ADD CONSTRAINT fk_productbudget_budget_fkbudget_rowid FOREIGN KEY (fk_budget) REFERENCES llx_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
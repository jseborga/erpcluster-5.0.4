ALTER TABLE llx_budget_task_resource ADD UNIQUE uk_unique (fk_budget_task, ref);
ALTER TABLE llx_budget_task_resource ADD percent_prod double DEFAULT '100' NOT NULL AFTER quant;
ALTER TABLE llx_budget_task_resource ADD amount_noprod double DEFAULT '0' NOT NULL AFTER percent_prod;
ALTER TABLE llx_budget_task_resource ADD fk_budget_task_comple INTEGER NULL DEFAULT '0' AFTER fk_product_budget;
ALTER TABLE llx_budget_task_resource ADD formula VARCHAR( 200 ) NULL AFTER rang;
ALTER TABLE llx_budget_task_resource ADD formula_res DOUBLE NULL AFTER formula;
ALTER TABLE llx_budget_task_resource ADD formula_quant DOUBLE NULL AFTER formula_res;
ALTER TABLE llx_budget_task_resource ADD formula_factor DOUBLE NULL AFTER formula_quant;
ALTER TABLE llx_budget_task_resource ADD formula_prod DOUBLE NULL AFTER formula_factor;
ALTER TABLE llx_budget_task_resource ADD priority TINYINT NOT NULL DEFAULT '0' AFTER rang;
ALTER TABLE llx_budget_task_resource ADD units DOUBLE(24,8) NULL DEFAULT '0' AFTER priority;
ALTER TABLE llx_budget_task_resource ADD commander TINYINT NULL DEFAULT '0' AFTER units;
ALTER TABLE llx_budget_task_resource ADD performance DOUBLE(24,8) NULL DEFAULT '0' AFTER commander;
ALTER TABLE llx_budget_task_resource ADD price_productive DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER performance;
ALTER TABLE llx_budget_task_resource ADD price_improductive DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER price_productive;

ALTER TABLE llx_budget_task_resource ADD CONSTRAINT fk_budgettaskresource_budgettask_fkbudgettask_rowid FOREIGN KEY (fk_budget_task) REFERENCES llx_budget_task(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_budget_task_resource ADD CONSTRAINT fk_budgettaskresource_productbudget_fkproductbudget_rowid FOREIGN KEY (fk_product_budget) REFERENCES llx_product_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
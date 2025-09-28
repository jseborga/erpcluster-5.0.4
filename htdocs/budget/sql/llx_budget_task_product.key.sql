ALTER TABLE llx_budget_task_product ADD UNIQUE KEY uk_unique (fk_budget_task,ref);

ALTER TABLE llx_budget_task_product ADD CONSTRAINT fk_budgettaskproduct_budgettask_fk_budgettask_rowid FOREIGN KEY (fk_budget_task) REFERENCES llx_budget_task(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_budget_task_product ADD CONSTRAINT fk_budgettaskproduct_productbudget_fkproductbudget_rowid FOREIGN KEY (fk_product_budget) REFERENCES llx_product_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
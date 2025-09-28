ALTER TABLE llx_budget_task_production ADD UNIQUE KEY uk_unique (fk_budget_task,fk_variable,fk_budget_task_product);

ALTER TABLE llx_budget_task_production ADD CONSTRAINT fk_budgettaskproduction_budgettask_fkbudgettask_rowid FOREIGN KEY (fk_budget_task) REFERENCES llx_budget_task(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_budget_task_production ADD CONSTRAINT fk_budgettaskproduction_productbudget_fkproductbudget_rowid FOREIGN KEY (fk_product_budget) REFERENCES llx_product_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE llx_budget_task_add ADD UNIQUE uk_unique (fk_budget_task);
ALTER TABLE llx_budget_task_add ADD complementary VARCHAR( 1 ) NOT NULL DEFAULT '0' AFTER fk_type;
ALTER TABLE llx_budget_task_add CHANGE unit_budget double(24,8) NOT NULL DEFAULT '0';
ALTER TABLE llx_budget_task_add ADD total_amount DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER unit_amount;
ALTER TABLE llx_budget_task_add ADD hour_production DOUBLE(24,8) NULL DEFAULT '0' AFTER total_amount;

ALTER TABLE llx_budget_task_add ADD CONSTRAINT llx_budgettaskadd_budgettask_fkbudgettask_rowid FOREIGN KEY (fk_budget_task) REFERENCES llx_budget_task(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
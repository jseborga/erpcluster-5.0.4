ALTER TABLE llx_budget_task ADD INDEX idx_budget_task_fk_budget (fk_budget);
ALTER TABLE llx_budget_task ADD INDEX idx_budget_task_fk_user_creat (fk_user_creat);
ALTER TABLE llx_budget_task ADD INDEX idx_budget_task_fk_user_valid (fk_user_valid);

ALTER TABLE llx_budget_task ADD amount DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER planned_workload;
ALTER TABLE llx_budget_task ADD formula VARCHAR(100) NULL DEFAULT NULL AFTER amount;
ALTER TABLE llx_budget_task ADD manual_performance TINYINT NOT NULL DEFAULT '0' AFTER formula;

ALTER TABLE llx_budget_task ADD CONSTRAINT fk_budgettask_budget_fk_budget_rowid FOREIGN KEY (fk_budget) REFERENCES llx_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

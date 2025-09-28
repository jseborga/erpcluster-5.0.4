ALTER TABLE llx_budget_task_duration ADD UNIQUE KEY uk_unique (fk_task);

ALTER TABLE llx_budget_task_duration ADD CONSTRAINT fk_budgettaskduration_budgettask_fkbudgettask_rowid FOREIGN KEY (fk_budget_task) REFERENCES llx_budget_task(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;


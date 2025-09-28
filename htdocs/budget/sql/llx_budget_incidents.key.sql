ALTER TABLE llx_budget_incidents ADD UNIQUE uk_unique (entity, fk_budget, ref);

ALTER TABLE llx_budget_incidents ADD CONSTRAINT fk_budgetincidents_budget_fkbudget_rowid FOREIGN KEY (fk_budget) REFERENCES llx_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
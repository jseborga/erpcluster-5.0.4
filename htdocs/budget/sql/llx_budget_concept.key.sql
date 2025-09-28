ALTER TABLE llx_budget_concept ADD UNIQUE uk_unique (fk_budget,ref);

ALTER TABLE llx_budget_concept ADD type VARCHAR(30) NULL DEFAULT NULL AFTER label;
ALTER TABLE llx_budget_concept ADD amount_def DOUBLE(24,8) NULL DEFAULT '0' AFTER type;

ALTER TABLE llx_budget_concept ADD CONSTRAINT fk_budgetconcept_budget_fk_budget_rowid FOREIGN KEY (fk_budget) REFERENCES llx_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
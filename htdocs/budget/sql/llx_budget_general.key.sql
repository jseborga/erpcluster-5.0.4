ALTER TABLE llx_budget_general ADD UNIQUE uk_unique (fk_budget);
ALTER TABLE llx_budget_general CHANGE second_currency second_currency VARCHAR( 30 ) NULL DEFAULT NULL;
ALTER TABLE llx_budget_general ADD base_currency VARCHAR(30) NOT NULL AFTER exchange_rate;
ALTER TABLE llx_budget_general CHANGE date_create datec DATE NOT NULL;
ALTER TABLE llx_budget_general CHANGE date_mod datem DATE NOT NULL;

ALTER TABLE llx_budget_general ADD CONSTRAINT fk_budgetgeneral_budget_fkbudget_rowid FOREIGN KEY (fk_budget) REFERENCES llx_budget(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
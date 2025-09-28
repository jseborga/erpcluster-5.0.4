ALTER TABLE llx_poa_workflow ADD UNIQUE KEY fk_poa_prev (fk_poa_prev);
ALTER TABLE llx_poa_workflow ADD deadlines INTEGER NOT NULL DEFAULT '0' AFTER fk_poa_prev;

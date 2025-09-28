ALTER TABLE llx_poa_process ADD UNIQUE KEY uk_unique (entity, gestion, ref);
ALTER TABLE llx_poa_process ADD cuce VARCHAR(30) NULL AFTER ref_pac;
ALTER TABLE llx_poa_process ADD code_process VARCHAR(30) NULL AFTER cuce;
ALTER TABLE llx_poa_process ADD doc_pac TINYINT NULL AFTER doc_informe_lega;
ALTER TABLE llx_poa_process ADD doc_prop TINYINT NULL AFTER doc_pac;
ALTER TABLE llx_poa_process ADD fk_soc INT NULL AFTER doc_prop;
ALTER TABLE llx_poa_process ADD justification TEXT NULL AFTER label;

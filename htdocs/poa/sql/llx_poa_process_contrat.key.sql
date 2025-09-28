ALTER TABLE llx_poa_process_contrat ADD UNIQUE uk_unique (fk_poa_process,fk_contrat);
ALTER TABLE llx_poa_process_contrat ADD date_order_proceed DATE NULL AFTER date_create;
ALTER TABLE llx_poa_process_contrat ADD date_provisional DATE NULL AFTER date_order_proceed;
ALTER TABLE llx_poa_process_contrat ADD date_final DATE NULL AFTER date_provisional;
ALTER TABLE llx_poa_process_contrat ADD date_nonconformity DATE NULL AFTER date_final;
ALTER TABLE llx_poa_process_contrat ADD nonconformity tinyint NULL AFTER date_nonconformity;
ALTER TABLE llx_poa_process_contrat ADD motif TEXT NULL AFTER nonconformity;

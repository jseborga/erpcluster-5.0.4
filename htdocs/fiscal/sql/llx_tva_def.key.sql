ALTER TABLE llx_tva_def DROP INDEX uk_unique;
ALTER TABLE llx_tva_def ADD entity INTEGER NOT NULL DEFAULT '1' AFTER rowid;
ALTER TABLE llx_tva_def ADD UNIQUE uk_unique (entity,fk_pays, code_facture, code_tva);
ALTER TABLE llx_tva_def ADD against_account VARCHAR(32) NULL  AFTER accountancy_code;

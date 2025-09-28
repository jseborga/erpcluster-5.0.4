ALTER TABLE llx_facture_bank_status ADD UNIQUE uk_unique (fk_facture,fk_bank_status);
ALTER TABLE llx_facture_bank_status ADD UNIQUE uk_unique_facture (fk_facture);
ALTER TABLE llx_facture_bank_status ADD label TEXT NULL AFTER exchange;
ALTER TABLE llx_facture_bank_status ADD fk_facture_rel INTEGER NULL DEFAULT '0' AFTER label;
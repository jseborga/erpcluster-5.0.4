ALTER TABLE llx_paiementfourn_advance ADD UNIQUE uk_unique(entity,ref);
ALTER TABLE llx_paiementfourn_advance ADD fk_facture integer DEFAULT '0' NULL after fk_soc;
ALTER TABLE llx_paiementfourn_advance ADD model_pdf VARCHAR(50) NULL AFTER fk_bank;

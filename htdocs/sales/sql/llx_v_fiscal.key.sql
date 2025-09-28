ALTER TABLE llx_v_fiscal ADD UNIQUE KEY uk_vfiscal_facture (fk_facture);
ALTER TABLE llx_v_fiscal ADD UNIQUE KEY uk_vfiscal_entity_nfiscal_nroautoriz (entity,nfiscal,num_autoriz);
ALTER TABLE llx_v_fiscal ADD date_create DATETIME NOT NULL AFTER valret5;
ALTER TABLE llx_v_fiscal ADD fk_user_create integer NOT NULL AFTER date_create;
ALTER TABLE llx_v_fiscal ADD statut_print TINYINT NULL AFTER fk_user_create;
ALTER TABLE llx_v_fiscal ADD amount_payment DOUBLE(20,4) NULL DEFAULT '0' AFTER valret5;
ALTER TABLE llx_v_fiscal ADD amount_balance DOUBLE(20,4) NULL DEFAULT '0' AFTER amount_payment;

ALTER TABLE llx_projet_paiementdet CHANGE quant qty double NOT NULL;
ALTER TABLE llx_projet_paiementdet CHANGE quant_ant qty_ant double NOT NULL;
ALTER TABLE llx_projet_paiementdet ADD fk_objectdet INTEGER NOT NULL DEFAULT '0' AFTER object;
ALTER TABLE llx_projet_paiementdet ADD objectdet VARCHAR(100) NOT NULL AFTER fk_objectdet;


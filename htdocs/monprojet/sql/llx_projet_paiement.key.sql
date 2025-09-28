ALTER TABLE llx_projet_paiement ADD UNIQUE uk_unique (fk_projet, ref);
ALTER TABLE llx_projet_paiement ADD total_tva DOUBLE NULL DEFAULT '0' AFTER date_request;
ALTER TABLE llx_projet_paiement CHANGE amount total_ht DOUBLE NULL DEFAULT '0';
ALTER TABLE llx_projet_paiement ADD total_ttc DOUBLE NULL DEFAULT '0' AFTER total_ht;


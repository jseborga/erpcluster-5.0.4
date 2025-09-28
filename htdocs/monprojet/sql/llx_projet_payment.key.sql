ALTER TABLE llx_projet_payment ADD UNIQUE uk_unique(fk_projet,ref);
ALTER TABLE llx_projet_payment ADD date_request DATE NOT NULL AFTER date_payment;
ALTER TABLE llx_projet_payment ADD document TEXT NULL AFTER amount;
ALTER TABLE llx_projet_payment ADD detail TEXT NULL AFTER document;
ALTER TABLE llx_projet_payment ADD fk_facture INTEGER NULL DEFAULT '0' AFTER detail;
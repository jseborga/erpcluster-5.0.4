ALTER TABLE llx_facture_fourn_add ADD UNIQUE uk_unique (fk_facture_fourn);
ALTER TABLE llx_facture_fourn_add ADD INDEX idx_code_facture (code_facture);


ALTER TABLE llx_facture_fourn_add ADD amount_ice DOUBLE NOT NULL AFTER amountnofiscal;
ALTER TABLE llx_facture_fourn_add CHANGE num_autoriz num_autoriz VARCHAR(30) NULL DEFAULT NULL;
ALTER TABLE llx_facture_fourn_add ADD nit_company VARCHAR(30) NULL AFTER code_type_purchase;
ALTER TABLE llx_facture_fourn_add ADD INDEX idx_code_type_purchase (code_type_purchase);
ALTER TABLE llx_facture_fourn_add ADD fk_projet_task INTEGER NULL DEFAULT '0' AFTER fk_facture_fourn;
ALTER TABLE llx_facture_fourn_add ADD object VARCHAR(255) NULL AFTER fk_facture_fourn;
ALTER TABLE llx_facture_fourn_add ADD fk_object INTEGER NULL DEFAULT '0' AFTER object;
ALTER TABLE llx_facture_fourn_add ADD fk_departament INTEGER NULL DEFAULT NULL AFTER fk_object;

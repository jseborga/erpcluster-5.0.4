ALTER TABLE llx_purchase_request ADD UNIQUE uk_unique ( entity , ref );
ALTER TABLE llx_purchase_request ADD origin VARCHAR(150) NULL AFTER model_pdf;
ALTER TABLE llx_purchase_request ADD originid INTEGER NULL AFTER origin;
ALTER TABLE llx_purchase_request ADD date_delivery DATE NULL AFTER originid;
ALTER TABLE llx_purchase_request ADD fk_poa_prev INTEGER NULL DEFAULT '0' AFTER fk_projet;
ALTER TABLE llx_purchase_request ADD status_process TINYINT NOT NULL DEFAULT '0' AFTER status;
ALTER TABLE llx_purchase_request CHANGE date_delivery date_delivery DATETIME NULL DEFAULT NULL;
ALTER TABLE llx_purchase_request ADD status_purchase TINYINT NOT NULL DEFAULT '0' AFTER status_process;
ALTER TABLE llx_supplier_proposal_add ADD status TINYINT NOT NULL DEFAULT '0' AFTER tms;


ALTER TABLE llx_supplier_proposal_add ADD code_facture VARCHAR(12) NULL AFTER fk_purchase_request;
ALTER TABLE llx_supplier_proposal_add ADD code_type_purchase VARCHAR(12) NULL AFTER code_facture;
ALTER TABLE llx_supplier_proposal_add ADD fk_pays INTEGER NULL DEFAULT NULL AFTER fk_purchase_request;
ALTER TABLE llx_supplier_proposal_add ADD fk_province INTEGER NULL DEFAULT NULL AFTER fk_pays;
ALTER TABLE llx_supplier_proposal_add DROP fk_provincie;
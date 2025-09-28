ALTER TABLE llx_c_type_purchase ADD entity INTEGER NOT NULL AFTER rowid;
ALTER TABLE llx_c_type_purchase ADD fk_categorie INTEGER NULL DEFAULT '0' AFTER label;
ALTER TABLE llx_c_type_purchase ADD INDEX idx_code (code);
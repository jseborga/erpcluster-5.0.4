ALTER TABLE llx_c_type_facture ADD INDEX idx_code(code);
ALTER TABLE llx_c_type_facture ADD UNIQUE uk_unique (fk_pays,code);

ALTER TABLE llx_c_type_facture ADD fk_pays INTEGER NOT NULL AFTER rowid;

ALTER TABLE llx_c_type_facture ADD type TINYINT NOT NULL AFTER detail;
ALTER TABLE llx_c_type_facture ADD nit_required TINYINT NULL DEFAULT '0' AFTER type;
ALTER TABLE llx_c_type_facture CHANGE type type_fact TINYINT NOT NULL DEFAULT '0';
ALTER TABLE llx_c_type_facture ADD type_value TINYINT NOT NULL DEFAULT '0' AFTER type_fact;
ALTER TABLE llx_c_type_facture ADD active TINYINT NOT NULL DEFAULT '1' AFTER nit_required;
ALTER TABLE llx_c_type_facture ADD retention TINYINT NOT NULL DEFAULT '0' AFTER type_value;
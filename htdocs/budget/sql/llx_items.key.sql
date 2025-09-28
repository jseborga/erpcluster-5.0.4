ALTER TABLE llx_items ADD UNIQUE uk_unique(entity, ref);
ALTER TABLE llx_items ADD type TINYINT NOT NULL DEFAULT '0' AFTER fk_type_item;
ALTER TABLE llx_items ADD formula VARCHAR(100) NULL DEFAULT NULL AFTER amount;
ALTER TABLE llx_items CHANGE date_create datec DATE NOT NULL;
ALTER TABLE llx_items CHANGE date_mod datem DATE NOT NULL;
ALTER TABLE llx_items ADD fk_parent integer DEFAULT NULL AFTER fk_type_item;
ALTER TABLE llx_items ADD manual_performance TINYINT NOT NULL DEFAULT '0' AFTER formula;
ALTER TABLE llx_items ADD hour_production DOUBLE(24,8) NULL DEFAULT '0' AFTER manual_performance;
ALTER TABLE llx_items ADD version INTEGER DEFAULT '1' NOT NULL AFTER ref_ext;

ALTER TABLE llx_items DROP INDEX uk_unique;
ALTER TABLE llx_items ADD UNIQUE uk_unique (entity, ref, version);
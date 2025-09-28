ALTER TABLE llx_p_type_fol ADD UNIQUE uk_unique (entity, ref);
ALTER TABLE llx_p_type_fol ADD name_report TEXT NULL DEFAULT NULL AFTER details;
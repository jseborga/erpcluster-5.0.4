ALTER TABLE llx_p_period DROP INDEX uk_p_period_entity_ref_fk_proces;
ALTER TABLE llx_p_period ADD UNIQUE uk_p_period_entity_ref_fk_proces (entity, ref, fk_proces, fk_type_fol);

ALTER TABLE llx_p_period ADD model_pdf VARCHAR( 50 ) NULL AFTER anio ;
ALTER TABLE llx_p_period ADD status_app TINYINT NULL DEFAULT NULL AFTER date_close;


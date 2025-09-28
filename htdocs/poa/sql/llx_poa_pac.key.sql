ALTER TABLE llx_poa_pac ADD UNIQUE uk_unique (entity,gestion,fk_poa,fk_type_modality,ref);
ALTER TABLE llx_poa_pac CHANGE tms tms TIMESTAMP NULL;

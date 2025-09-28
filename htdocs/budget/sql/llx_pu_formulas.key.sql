ALTER TABLE llx_pu_formulas ADD UNIQUE INDEX uk_pformulas_entity_ref(entity,ref);
ALTER TABLE llx_pu_formulas CHANGE detail detail VARCHAR(100) NOT NULL;

ALTER TABLE llx_p_generic_table ADD ref varchar(10) NOT NULL AFTER entity;
ALTER TABLE llx_p_generic_table ADD UNIQUE INDEX uk_pgenerictable_entity_ref(entity,ref);


ALTER TABLE llx_cs_seatstype ADD UNIQUE INDEX uk_entity_code (entity,code);
ALTER TABLE llx_cs_seatstype ADD ref VARCHAR( 2 ) NOT NULL AFTER label;

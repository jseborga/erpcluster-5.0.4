ALTER TABLE llx_contab_transaction ADD UNIQUE uk_unique (entity,ref);
ALTER TABLE llx_contab_transaction ADD type_seat TINYINT DEFAULT NULL AFTER bits;
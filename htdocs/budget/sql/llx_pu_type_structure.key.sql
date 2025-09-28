ALTER TABLE llx_pu_type_structure ADD UNIQUE uk_unique (entity, code);

ALTER TABLE llx_pu_type_structure ADD status tinyint DEFAULT '0' AFTER tms;
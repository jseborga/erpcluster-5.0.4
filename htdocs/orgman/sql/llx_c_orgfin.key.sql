ALTER TABLE llx_c_orgfin ADD UNIQUE uk_unique (entity,ref);

ALTER TABLE llx_c_orgfin ADD ref_ext varchar(50) NULL AFTER detail;

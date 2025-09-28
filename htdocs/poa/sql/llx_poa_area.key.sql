ALTER TABLE llx_poa_area ADD UNIQUE UK_UNIQUE (entity, ref);
ALTER TABLE llx_poa_area ADD code_actor varchar(30) NULL DEFAULT '0' AFTER fk_father;

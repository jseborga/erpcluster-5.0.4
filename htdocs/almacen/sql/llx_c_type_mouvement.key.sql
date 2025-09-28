ALTER TABLE llx_c_type_mouvement ADD entity integer DEFAULT '1' NOT NULL AFTER rowid;
ALTER TABLE llx_c_type_mouvement ADD UNIQUE uk_unique (entity,code);
ALTER TABLE llx_c_type_mouvement ADD type varchar(1) NOT NULL AFTER label;
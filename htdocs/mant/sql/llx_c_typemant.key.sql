ALTER TABLE llx_c_typemant ADD entity integer NOT NULL AFTER rowid;
ALTER TABLE llx_c_typemant ADD UNIQUE uk_unique (entity,code);
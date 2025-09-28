ALTER TABLE llx_c_typeprocedure ADD landmark TINYINT NOT NULL DEFAULT '0' AFTER label;
ALTER TABLE llx_c_typeprocedure ADD colour varchar(6) NULL DEFAULT ' ' AFTER landmark;
ALTER TABLE llx_c_typeprocedure ADD sigla varchar(30) NULL DEFAULT ' ' AFTER label;

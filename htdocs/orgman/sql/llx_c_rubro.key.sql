ALTER TABLE llx_c_rubro ADD UNIQUE uk_unique (period_year, code);
ALTER TABLE llx_c_rubro DROP INDEX uk_code;
ALTER TABLE llx_c_rubro ADD code_father varchar(30) DEFAULT NULL AFTER detail;

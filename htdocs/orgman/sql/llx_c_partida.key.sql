ALTER TABLE llx_c_partida ADD UNIQUE uk_unique (period_year, code);
ALTER TABLE llx_c_partida DROP INDEX uk_code;
ALTER TABLE llx_c_partida ADD code_father varchar(30) DEFAULT NULL AFTER detail;
ALTER TABLE llx_c_partida ADD type tinyint DEFAULT '0' NOT NULL AFTER code_father;

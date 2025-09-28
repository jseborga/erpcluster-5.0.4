ALTER TABLE llx_c_partida ADD UNIQUE uk_unique (period_year, code);
ALTER TABLE llx_c_partida DROP INDEX uk_code;
ALTER TABLE llx_c_partida ADD fk_father integer DEFAULT '0' NULL AFTER detail;

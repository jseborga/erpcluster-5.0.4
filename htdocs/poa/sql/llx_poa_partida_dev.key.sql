ALTER TABLE llx_poa_partida_dev ADD UNIQUE uk_unique_ppc (fk_poa_partida_com);
ALTER TABLE llx_poa_partida_dev ADD fk_contrato INTEGER NULL DEFAULT '0' AFTER fk_contrat;
ALTER TABLE llx_poa_partida_dev ADD type_pay TINYINT NOT NULL DEFAULT '0' AFTER fk_contrato;

ALTER TABLE llx_poa_partida_com ADD UNIQUE uk_unique_ppp (fk_poa_partida_pre);
ALTER TABLE llx_poa_partida_com ADD fk_contrato integer NULL DEFAULT '0' AFTER fk_contrat;


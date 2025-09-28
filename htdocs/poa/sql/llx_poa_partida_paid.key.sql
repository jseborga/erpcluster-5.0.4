ALTER TABLE llx_poa_partida_paid ADD UNIQUE uk_unique (fk_poa_prev,fk_structure,fk_poa,fk_contrat,nro_paid,partida);

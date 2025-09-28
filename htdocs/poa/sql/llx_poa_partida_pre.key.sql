ALTER TABLE llx_poa_partida_pre ADD fk_user_create INTEGER NOT NULL AFTER amount;
ALTER TABLE llx_poa_partida_pre ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_poa_partida_pre ADD datec date NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_poa_partida_pre ADD datem date NOT NULL AFTER datec;
ALTER TABLE llx_poa_partida_pre ADD tms timestamp NOT NULL AFTER datem;


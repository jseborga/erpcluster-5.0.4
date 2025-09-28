ALTER TABLE llx_poa_poa ADD fk_user_create INTEGER NOT NULL AFTER version;
ALTER TABLE llx_poa_poa ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_poa_poa ADD datec DATE NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_poa_poa ADD datem DATE NOT NULL AFTER datec;
ALTER TABLE llx_poa_poa ADD tms TIMESTAMP NOT NULL AFTER datem;
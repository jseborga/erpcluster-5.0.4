ALTER TABLE llx_poa_structure ADD unit varchar(50) DEFAULT NULL;
ALTER TABLE llx_poa_structure ADD type TINYINT NOT NULL DEFAULT '1' AFTER gestion;
ALTER TABLE llx_poa_structure ADD fk_poa_objetive INTEGER NOT NULL AFTER type;
ALTER TABLE llx_poa_structure ADD fk_user_create INTEGER NOT NULL AFTER version;
ALTER TABLE llx_poa_structure ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_poa_structure ADD datec DATE NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_poa_structure ADD datem DATE NOT NULL AFTER datec;
ALTER TABLE llx_poa_structure ADD tms TIMESTAMP NOT NULL AFTER datem;

ALTER TABLE llx_poa_structure ADD fk_area_ej INTEGER DEFAULT '0' NOT NULL AFTER fk_area;
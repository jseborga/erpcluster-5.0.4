ALTER TABLE llx_poa_prev ADD UNIQUE KEY uk_unique (entity,period_year,nro_preventive);


ALTER TABLE llx_poa_prev ADD priority TINYINT NULL DEFAULT '0' AFTER amount;
ALTER TABLE llx_poa_prev ADD pseudonym VARCHAR(50) NULL AFTER label;
ALTER TABLE llx_poa_prev ADD fk_poa_activity INTEGER DEFAULT '0' NULL AFTER fk_area;
ALTER TABLE llx_poa_prev ADD origin VARCHAR(150) NULL AFTER fk_poa_activity;
ALTER TABLE llx_poa_prev ADD originid INTEGER DEFAULT '0' NULL AFTER origin;
ALTER TABLE llx_poa_prev ADD code_requirement VARCHAR(30) NOT NULL AFTER fk_purchase_request;
ALTER TABLE llx_poa_prev ADD fk_user_create INTEGER NOT NULL AFTER priority;
ALTER TABLE llx_poa_prev ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_poa_prev ADD datec date NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_poa_prev ADD datem date NOT NULL AFTER datec;
ALTER TABLE llx_poa_prev ADD tms timestamp NOT NULL AFTER datem;

ALTER TABLE llx_poa_prev ADD INDEX idx_origin_originid (origin, originid);
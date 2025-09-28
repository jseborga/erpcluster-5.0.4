ALTER TABLE llx_entrepot_bank_soc ADD fk_user integer DEFAULT 0 NULL AFTER numero_ip;
ALTER TABLE llx_entrepot_bank_soc ADD UNIQUE uk_unique_ip (entity,numero_ip,fk_entrepotid,series);
ALTER TABLE llx_entrepot_bank_soc ADD UNIQUE uk_unique_user (entity,fk_user,fk_entrepotid,series);
ALTER TABLE llx_entrepot_bank_soc ADD series VARCHAR(4) NOT NULL AFTER fk_subsidiaryid;
ALTER TABLE llx_entrepot_bank_soc CHANGE numero_ip numero_ip VARCHAR(15) NULL DEFAULT NULL;
ALTER TABLE llx_entrepot_bank_soc ADD fk_user_create integer NOT NULL AFTER series;
ALTER TABLE llx_entrepot_bank_soc ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_entrepot_bank_soc ADD date_create date NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_entrepot_bank_soc ADD date_mod date NOT NULL AFTER date_create;
ALTER TABLE llx_entrepot_bank_soc ADD tms timestamp NOT NULL AFTER date_mod;


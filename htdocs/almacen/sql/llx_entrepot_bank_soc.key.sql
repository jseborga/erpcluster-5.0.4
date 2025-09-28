ALTER TABLE llx_entrepot_bank_soc ADD fk_user integer DEFAULT 0 NULL AFTER numero_ip;
ALTER TABLE llx_entrepot_bank_soc ADD UNIQUE (entity, numero_ip,fk_user, fk_entrepotid);

ALTER TABLE llx_poa_activity ADD UNIQUE uk_unique (entity,gestion,nro_activity);

ALTER TABLE llx_poa_activity ADD fk_prev integer NULL DEFAULT '0' AFTER fk_prev_ant;

ALTER TABLE llx_poa_activity CHANGE date_create datec DATE NOT NULL;
ALTER TABLE llx_poa_activity ADD fk_user_mod integer NOT NULL DEFAULT '0' AFTER fk_user_create;
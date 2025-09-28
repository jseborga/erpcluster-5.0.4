ALTER TABLE llx_v_dosing ADD UNIQUE INDEX uk_v_dosing_entity_subsid_series_numini_numfin(entity,fk_subsidiaryid,series,num_ini,num_fin);
ALTER TABLE llx_v_dosing CHANGE series series VARCHAR(4) NOT NULL;
ALTER TABLE llx_v_dosing DROP INDEX uk_v_dosing_entity_subsid_series_numini_numfin;
ALTER TABLE llx_v_dosing ADD UNIQUE uk_v_dosing_entity_subsid_series_num_autoriz (entity, fk_subsidiaryid, series, num_autoriz);

ALTER TABLE llx_v_dosing CHANGE num_fin num_fin integer NOT NULL DEFAULT '0';
ALTER TABLE llx_v_dosing ADD activity TEXT NULL DEFAULT NULL AFTER descrip;
ALTER TABLE llx_v_dosing ADD fk_user_create integer AFTER activity;
ALTER TABLE llx_v_dosing ADD fk_user_mod integer AFTER fk_user_create;
ALTER TABLE llx_v_dosing ADD date_create date AFTER fk_user_mod;
ALTER TABLE llx_v_dosing ADD date_mod date AFTER date_create;
ALTER TABLE llx_v_dosing ADD tms timestamp AFTER date_mod;

ALTER TABLE llx_p_departament ADD UNIQUE INDEX uk_p_departament_entity_ref (entity,ref);
ALTER TABLE llx_p_departament ADD label varchar(255) NOT NULL AFTER ref;

ALTER TABLE llx_p_departament ADD fk_user_create integer DEFAULT '0' NOT NULL AFTER fk_user_resp;
ALTER TABLE llx_p_departament ADD fk_user_mod integer DEFAULT '0' NOT NULL AFTER fk_user_create;
ALTER TABLE llx_p_departament ADD datec date NULL AFTER fk_user_mod;
ALTER TABLE llx_p_departament ADD datem date NULL AFTER datec;
ALTER TABLE llx_p_departament ADD tms timestamp NOT NULL AFTER datem;
ALTER TABLE llx_p_departament ADD active tinyint DEFAULT '1' NOT NULL AFTER tms;
ALTER TABLE llx_p_departament ADD status tinyint DEFAULT '1' NOT NULL AFTER active;




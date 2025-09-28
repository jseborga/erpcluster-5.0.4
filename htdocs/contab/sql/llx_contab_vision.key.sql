ALTER TABLE llx_contab_vision ADD UNIQUE INDEX uk_entity_ref_account (entity,ref,account);
ALTER TABLE llx_contab_vision ADD fk_user_create INTEGER NULL AFTER fk_accountfin;
ALTER TABLE llx_contab_vision ADD fk_user_mod INTEGER NULL AFTER fk_user_create;
ALTER TABLE llx_contab_vision ADD datec DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_contab_vision ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_contab_vision ADD tms TIMESTAMP NOT NULL AFTER datem;

ALTER TABLE llx_contab_vision ADD fk_parent INTEGER NULL DEFAULT '0' AFTER ref;

ALTER TABLE llx_contab_vision CHANGE statut status TINYINT NOT NULL;
ALTER TABLE llx_contab_vision DROP INDEX uk_entity_ref_account;
ALTER TABLE llx_contab_vision ADD UNIQUE uk_entity_ref_account (entity, ref, account, line);

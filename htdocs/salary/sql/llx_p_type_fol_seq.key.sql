ALTER TABLE llx_p_type_fol_seq ADD UNIQUE KEY UK_UNIQUE (fk_type_fol,sequen,ref_concept);


ALTER TABLE llx_p_type_fol_seq ADD fk_user_create INTEGER NULL AFTER state;
ALTER TABLE llx_p_type_fol_seq ADD fk_user_mod INTEGER NULL AFTER fk_user_create;
ALTER TABLE llx_p_type_fol_seq ADD datec DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_p_type_fol_seq ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_p_type_fol_seq ADD tms TIMESTAMP NULL AFTER datem;
ALTER TABLE llx_p_type_fol_seq ADD status TINYINT NOT NULL DEFAULT '0' AFTER tms;
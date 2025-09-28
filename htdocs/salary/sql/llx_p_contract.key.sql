ALTER TABLE llx_p_contract ADD UNIQUE INDEX uk_p_contract_ref_user (ref,fk_user,date_ini);
ALTER TABLE llx_p_contract ADD fk_account integer NULL DEFAULT '0' AFTER fk_cc;
ALTER TABLE llx_p_contract ADD fk_unit integer NULL DEFAULT '0' AFTER fk_account;
ALTER TABLE llx_p_contract ADD fk_user_create integer NOT NULL AFTER afp;
ALTER TABLE llx_p_contract ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_p_contract ADD date_create date NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_p_contract ADD date_mod date NOT NULL AFTER date_create;
ALTER TABLE llx_p_contract ADD tms timestamp NOT NULL AFTER date_mod;
ALTER TABLE llx_p_contract ADD number_item VARCHAR(20) NULL DEFAULT NULL AFTER fk_unit;
ALTER TABLE llx_p_contract CHANGE nivel nivel VARCHAR(40) NULL DEFAULT NULL;
ALTER TABLE llx_p_contract ADD unit_cost DOUBLE(24,5) NULL DEFAULT '0' AFTER basic_fixed;


ALTER TABLE llx_p_salary_present ADD UNIQUE INDEX uk_salary_present (entity,fk_proces,fk_type_fol,fk_concept,fk_period,fk_user);
ALTER TABLE llx_p_salary_present ADD sequen INTEGER NOT NULL AFTER fk_cc;
ALTER TABLE llx_p_salary_present ADD date_mod DATE NOT NULL AFTER date_create;
ALTER TABLE llx_p_salary_present ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_p_salary_present ADD fk_account integer NULL DEFAULT '0' AFTER fk_user_mod;
ALTER TABLE llx_p_salary_present ADD payment_state tinyint NULL DEFAULT '0' AFTER fk_account;
ALTER TABLE llx_p_salary_present ADD tms TIMESTAMP NOT NULL AFTER payment_state;
ALTER TABLE llx_p_salary_present CHANGE amount amount DOUBLE( 24, 5 ) NOT NULL ;
ALTER TABLE llx_p_salary_present CHANGE fk_cc fk_cc INTEGER NOT NULL ;


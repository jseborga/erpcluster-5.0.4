ALTER TABLE llx_p_salary_history ADD UNIQUE INDEX uk_salary_history_entity_fkproces_fktypefol_fkconcept_fk_period_fkuser (entity,fk_proces,fk_type_fol,fk_concept,fk_period,fk_user);
ALTER TABLE llx_p_salary_history ADD sequen INTEGER NOT NULL DEFAULT '0' AFTER fk_cc;
ALTER TABLE llx_p_salary_history ADD date_mod DATE NOT NULL AFTER date_create;
ALTER TABLE llx_p_salary_history ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_p_salary_history ADD fk_account integer NULL DEFAULT '0' AFTER fk_user_mod;
ALTER TABLE llx_p_salary_history ADD payment_state tinyint NULL DEFAULT '0' AFTER fk_account;
ALTER TABLE llx_p_salary_history ADD tms TIMESTAMP NOT NULL AFTER payment_state;
ALTER TABLE llx_p_salary_history CHANGE amount amount DOUBLE( 24, 5 ) NOT NULL ;
ALTER TABLE llx_p_salary_history CHANGE fk_cc fk_cc INTEGER NOT NULL ;
ALTER TABLE llx_p_salary_history ADD ref VARCHAR(30) NULL AFTER entity;
ALTER TABLE llx_p_salary_history ADD period_year MEDIUMINT NULL AFTER ref;


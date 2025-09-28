ALTER TABLE llx_member_vacation ADD UNIQUE uk_unique (fk_member, period_year);
ALTER TABLE llx_member_vacation ADD fk_user_app INTEGER NULL DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_member_vacation ADD datea date NULL DEFAULT NULL AFTER datem;

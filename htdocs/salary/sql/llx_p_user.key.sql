ALTER TABLE llx_p_user ADD UNIQUE INDEX uk_p_user (fk_user);

ALTER TABLE llx_p_user CHANGE lastname lastname VARCHAR(40) NULL;
ALTER TABLE llx_p_user ADD days_assigned integer NULL AFTER dependents;
ALTER TABLE llx_p_user ADD fk_user_create integer NULL AFTER days_assigned;
ALTER TABLE llx_p_user ADD fk_user_mod integer NULL AFTER fk_user_create;
ALTER TABLE llx_p_user ADD datec date NULL AFTER fk_user_mod;
ALTER TABLE llx_p_user ADD datem date NULL AFTER datec;
ALTER TABLE llx_p_user ADD tms timestamp NULL AFTER datem;
ALTER TABLE llx_p_user ADD status tinyint DEFAULT '1' AFTER tms;
ALTER TABLE llx_p_user CHANGE sex sex VARCHAR(2) NULL DEFAULT NULL;
ALTER TABLE llx_p_user CHANGE day_assigned day_assigned tinyint NULL DEFAULT 0;

ALTER TABLE llx_p_user ADD registration VARCHAR(30) NULL DEFAULT NULL AFTER docum;


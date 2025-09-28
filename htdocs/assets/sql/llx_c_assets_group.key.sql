ALTER TABLE llx_c_assets_group ADD entity INTEGER DEFAULT '1' NOT NULL AFTER rowid;
ALTER TABLE llx_c_assets_group ADD UNIQUE uk_unique (entity,code);
ALTER TABLE llx_c_assets_group ADD account_accounting varchar(30) NULL AFTER percent;
ALTER TABLE llx_c_assets_group CHANGE percent useful_life DOUBLE NULL DEFAULT NULL ;
ALTER TABLE llx_c_assets_group ADD account_spending VARCHAR(30) NULL AFTER account_accounting;
ALTER TABLE llx_c_assets_group ADD fk_method_dep INTEGER NULL AFTER percent;

ALTER TABLE llx_c_assets_group ADD INDEX idx_code (code);
ALTER TABLE llx_c_assets_group ADD description TEXT NULL AFTER useful_life;
ALTER TABLE llx_c_assets_group ADD depreciate TINYINT NULL DEFAULT '0' AFTER account_spending;
ALTER TABLE llx_c_assets_group ADD toupdate TINYINT NULL DEFAULT '0' AFTER depreciate;
ALTER TABLE llx_c_assets_group ADD fk_user_create INTEGER NOT NULL AFTER toupdate;
ALTER TABLE llx_c_assets_group ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_c_assets_group ADD datec DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_c_assets_group ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_c_assets_group ADD tms TIMESTAMP NULL AFTER datem;

ALTER TABLE llx_c_assets_group CHANGE label label VARCHAR(255) NOT NULL;
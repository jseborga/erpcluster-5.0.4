ALTER TABLE llx_licences ADD date_ini_ejec datetime NULL after date_fin;
ALTER TABLE llx_licences ADD date_fin_ejec datetime NULL after date_ini_ejec;
ALTER TABLE llx_licences ADD datem date DEFAULT NULL after tms;
ALTER TABLE llx_licences ADD datea datetime DEFAULT NULL after datem;
ALTER TABLE llx_licences ADD dater datetime DEFAULT NULL after datea;

ALTER TABLE llx_licences ADD fk_user_aprob integer DEFAULT NULL after fk_user_mod;
ALTER TABLE llx_licences ADD fk_user_reg integer DEFAULT NULL after fk_user_aprob;
ALTER TABLE llx_licences ADD halfday INTEGER NULL DEFAULT '0' AFTER detail;
ALTER TABLE llx_licences CHANGE date_ini_ejec date_ini_ejec DATETIME NULL DEFAULT NULL;
ALTER TABLE llx_licences ADD fk_user_rev INTEGER NULL DEFAULT NULL AFTER fk_user_aprob;
ALTER TABLE llx_licences ADD datev DATE NULL DEFAULT NULL AFTER dater;
ALTER TABLE llx_licences ADD halfday_ejec INTEGER NULL DEFAULT NULL AFTER halfday;


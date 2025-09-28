ALTER TABLE llx_mant_programming  ADD UNIQUE KEY uk_unique (entity,fk_asset,typemant,frequency);

ALTER TABLE llx_mant_programming ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_mant_programming ADD datec DATE NULL DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_mant_programming ADD datem DATE NULL DEFAULT NULL AFTER datec;
ALTER TABLE llx_mant_programming ADD active TINYINT NOT NULL DEFAULT '1' AFTER datem;
ALTER TABLE llx_mant_programming ADD fk_equipment INTEGER NOT NULL DEFAULT '0' AFTER fk_asset;
ALTER TABLE llx_mant_programming DROP INDEX uk_unique;
ALTER TABLE llx_mant_programming ADD UNIQUE uk_unique (entity, fk_equipment, typemant, frequency);

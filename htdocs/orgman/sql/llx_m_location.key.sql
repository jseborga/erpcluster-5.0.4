ALTER TABLE llx_m_location ADD UNIQUE uk_unique (fk_property, detail);

ALTER TABLE llx_m_location CHANGE fk_user_modify fk_user_mod INTEGER NULL DEFAULT NULL;
ALTER TABLE llx_m_location ADD safety TINYINT NOT NULL DEFAULT '0' AFTER detail;
ALTER TABLE llx_m_location ADD fk_user_create INTEGER NULL DEFAULT '0' AFTER safety;
ALTER TABLE llx_m_location ADD fk_user_mod INTEGER NULL DEFAULT '0' AFTER fk_user_create;
ALTER TABLE llx_m_location ADD datec DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_m_location ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_m_location ADD tms TIMESTAMP NULL AFTER datem;
ALTER TABLE llx_m_location ADD statut TINYINT NULL DEFAULT '0' AFTER tms;
ALTER TABLE llx_m_location CHANGE statut status TINYINT NOT NULL DEFAULT '1';

ALTER TABLE llx_m_equipment ADD UNIQUE uk_mequipment_entity_ref (entity, ref);

ALTER TABLE llx_m_equipment CHANGE nom label varchar(255) NOT NULL;
ALTER TABLE llx_m_equipment CHANGE statut status tinyint DEFAULT '0' NOT NULL;
ALTER TABLE llx_m_equipment ADD metered tinyint NOT NULL DEFAULT '0' AFTER label;
ALTER TABLE llx_m_equipment ADD accountant double NOT NULL DEFAULT '0' AFTER metered;
ALTER TABLE llx_m_equipment ADD fk_unit integer NOT NULL DEFAULT '0' AFTER accountant;
ALTER TABLE llx_m_equipment ADD margin integer NOT NULL DEFAULT '0' AFTER fk_unit;
ALTER TABLE llx_m_equipment ADD fk_asset integer NOT NULL DEFAULT '0' AFTER fk_location;
ALTER TABLE llx_m_equipment ADD hour_cost DOUBLE NOT NULL DEFAULT '0' AFTER fk_asset;
ALTER TABLE llx_m_equipment ADD code_program varchar(30) NULL DEFAULT '0' AFTER hour_cost;
ALTER TABLE llx_m_equipment ADD fk_user_create integer NOT NULL AFTER code_program;
ALTER TABLE llx_m_equipment ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_m_equipment ADD datec date NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_m_equipment ADD datem date NOT NULL AFTER datec;
ALTER TABLE llx_m_equipment ADD active tinyint NOT NULL DEFAULT '0' AFTER tms;
ALTER TABLE llx_m_equipment CHANGE accountant accountant double DEFAULT '0' NOT NULL;
ALTER TABLE llx_m_equipment ADD accountant_last double NOT NULL DEFAULT '0' AFTER accountant;
ALTER TABLE llx_m_equipment ADD accountant_mant double NOT NULL DEFAULT '0' AFTER accountant_last;
ALTER TABLE llx_m_equipment ADD accountant_mante DOUBLE DEFAULT '0' NOT NULL AFTER accountant_mant;

ALTER TABLE llx_m_equipment ADD fk_group INTEGER NULL AFTER fk_asset;
ALTER TABLE llx_m_equipment CHANGE code_program code_program VARCHAR(30) NULL DEFAULT '0';
ALTER TABLE llx_m_equipment CHANGE fk_equipment_program fk_equipment_program INTEGER NULL DEFAULT '0';
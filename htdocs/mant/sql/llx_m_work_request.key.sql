ALTER TABLE llx_m_work_request ADD UNIQUE uk_unique (entity, ref);
ALTER TABLE llx_m_work_request ADD fk_soc INTEGER NULL AFTER fk_location;
ALTER TABLE llx_m_work_request ADD description_prog TEXT DEFAULT NULL AFTER tokenreg;
ALTER TABLE llx_m_work_request ADD date_ini_prog DATETIME DEFAULT NULL AFTER description_prog;
ALTER TABLE llx_m_work_request ADD date_fin_prog DATETIME DEFAULT NULL AFTER date_ini_prog;
ALTER TABLE llx_m_work_request ADD speciality_prog VARCHAR(30) DEFAULT NULL AFTER date_fin_prog;
ALTER TABLE llx_m_work_request ADD fk_equipment_prog INTEGER DEFAULT NULL AFTER speciality_prog;
ALTER TABLE llx_m_work_request ADD fk_property_prog INTEGER DEFAULT NULL AFTER fk_equipment_prog;
ALTER TABLE llx_m_work_request ADD fk_location_prog INTEGER DEFAULT NULL AFTER fk_property_prog;
ALTER TABLE llx_m_work_request ADD typemant_prog VARCHAR(30) DEFAULT NULL AFTER fk_location_prog;
ALTER TABLE llx_m_work_request ADD fk_user_prog INTEGER DEFAULT NULL AFTER typemant_prog;
ALTER TABLE llx_m_work_request ADD image_ini varchar(200) DEFAULT NULL AFTER fk_user_prog;
ALTER TABLE llx_m_work_request ADD speciality varchar(30) DEFAULT NULL AFTER detail_problem;
ALTER TABLE llx_m_work_request ADD fk_type_repair INTEGER NULL AFTER fk_soc;

ALTER TABLE llx_m_work_request ADD fk_user_create integer NOT NULL DEFAULT '0' AFTER image_ini;
ALTER TABLE llx_m_work_request ADD fk_user_mod integer NOT NULL DEFAULT '0' AFTER fk_user_create;
ALTER TABLE llx_m_work_request ADD datec date DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_m_work_request ADD datem date DEFAULT NULL AFTER datec;
ALTER TABLE llx_m_work_request CHANGE statut status tinyint DEFAULT '0' NOT NULL;


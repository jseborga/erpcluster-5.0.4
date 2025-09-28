ALTER TABLE llx_m_jobs ADD UNIQUE uk_mjobs_entity_ref (entity, ref);

ALTER TABLE llx_m_jobs ADD group_task tinyint DEFAULT '0' NULL AFTER description_job;
ALTER TABLE llx_m_jobs ADD task tinyint DEFAULT '1' NULL AFTER group_task;

ALTER TABLE llx_m_jobs ADD fk_user_create integer NOT NULL DEFAULT '0' AFTER tokenreg;
ALTER TABLE llx_m_jobs ADD fk_user_mod integer NOT NULL DEFAULT '0' AFTER fk_user_create;
ALTER TABLE llx_m_jobs ADD datec date DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_m_jobs ADD datem date DEFAULT NULL AFTER datec;
ALTER TABLE llx_m_jobs CHANGE statut status tinyint DEFAULT '0' NOT NULL;
ALTER TABLE llx_m_jobs ADD fk_type_repair INTEGER NOT NULL DEFAULT '0' AFTER fk_location;
ALTER TABLE llx_m_jobs CHANGE email email VARCHAR(200) NULL;
ALTER TABLE llx_m_jobs ADD fk_departament_assign INTEGER NULL DEFAULT '0' AFTER address_ip;
ALTER TABLE llx_m_property ADD UNIQUE uk_mproperty_entity_ref (entity, ref);
ALTER TABLE llx_m_property ADD fk_country INTEGER NULL AFTER address;
ALTER TABLE llx_m_property ADD fk_state INTEGER NULL AFTER fk_country;
ALTER TABLE llx_m_property ADD fk_user_create INTEGER NOT NULL AFTER fk_state;
ALTER TABLE llx_m_property ADD date_create DATE NOT NULL AFTER fk_user_create;
ALTER TABLE llx_m_property ADD tms TIMESTAMP NOT NULL AFTER date_create;
ALTER TABLE llx_m_property ADD status TINYINT DEFAULT '0' NOT NULL AFTER tms;


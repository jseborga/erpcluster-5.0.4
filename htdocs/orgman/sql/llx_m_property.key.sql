ALTER TABLE llx_m_property ADD UNIQUE uk_mproperty_entity_ref (entity, ref);
ALTER TABLE llx_m_property ADD label varchar(255) NOT NULL AFTER ref;
ALTER TABLE llx_m_property ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_m_property CHANGE date_create datec DATE NOT NULL;
ALTER TABLE llx_m_property ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_m_property DROP date_create;
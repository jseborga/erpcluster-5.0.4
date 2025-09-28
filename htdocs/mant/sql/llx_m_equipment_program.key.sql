ALTER TABLE llx_m_equipment_program ADD UNIQUE uk_unique (fk_equipment,ref);

ALTER TABLE llx_m_equipment_program ADD fk_parent_previous INTEGER NOT NULL DEFAULT '0' AFTER fk_equipment;
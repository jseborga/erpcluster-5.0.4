ALTER TABLE llx_pu_structure_det ADD UNIQUE uk_unique (entity, ref_structure, type_structure, sequen);
ALTER TABLE llx_pu_structure_det CHANGE detail detail VARCHAR(100) NOT NULL;

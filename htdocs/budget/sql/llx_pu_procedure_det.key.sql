ALTER TABLE llx_pu_structure_det ADD UNIQUE uk_unique (entity, ref_structure, type_structure, sequen);

ALTER TABLE llx_pu_structure_det ADD status_print TINYINT NOT NULL DEFAULT '0' AFTER formula;
ALTER TABLE llx_pu_structure_det ADD status_print_det TINYINT NOT NULL DEFAULT '1' AFTER status_print;
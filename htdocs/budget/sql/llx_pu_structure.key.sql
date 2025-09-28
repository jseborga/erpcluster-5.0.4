ALTER TABLE llx_pu_structure ADD UNIQUE uk_unique (entity, ref, type_structure);
ALTER TABLE llx_pu_structure ADD type_structure VARCHAR(30) NOT NULL AFTER fk_categorie;
ALTER TABLE llx_pu_structure ADD date_mod DATE NOT NULL AFTER date_create;
ALTER TABLE llx_pu_structure DROP fk_budget;
ALTER TABLE llx_pu_structure DROP fk_projet;
ALTER TABLE llx_pu_structure ADD group_structure VARCHAR(2) NOT NULL AFTER ref;
ALTER TABLE llx_pu_structure ADD complementary VARCHAR( 1 ) NOT NULL DEFAULT '1' AFTER type_structure;
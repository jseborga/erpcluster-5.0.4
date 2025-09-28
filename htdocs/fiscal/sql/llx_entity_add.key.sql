ALTER TABLE llx_entity_add ADD UNIQUE INDEX uk_unique(fk_entity);
ALTER TABLE llx_entity_add ADD CONSTRAINT idk_fk_entity FOREIGN KEY (fk_entity) REFERENCES llx_entity(rowid);
ALTER TABLE llx_entity ADD CONSTRAINT idk_fk_user_creat FOREIGN KEY (fk_user_creat) REFERENCES llx_user(rowid)

ALTER TABLE llx_entity_add ADD CONSTRAINT idk_entityadd_fk_entity FOREIGN KEY (fk_entity) REFERENCES llx_entity_add(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
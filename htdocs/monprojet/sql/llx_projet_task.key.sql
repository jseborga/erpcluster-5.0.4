ALTER TABLE llx_projet_task DROP INDEX uk_projet_task_ref;
ALTER TABLE llx_projet_task ADD UNIQUE uk_projet_task_ref (fk_projet, ref, entity);
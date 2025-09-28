ALTER TABLE llx_projet_task_add ADD UNIQUE uk_unique(fk_task);
ALTER TABLE llx_projet_task_add ADD c_view TINYINT NULL DEFAULT '0' AFTER c_grupo;
ALTER TABLE llx_projet_task_add ADD level TINYINT NULL DEFAULT '0' AFTER c_grupo;
ALTER TABLE llx_projet_task_add ADD order_ref INTEGER NULL DEFAULT '0' AFTER detail_close;
ALTER TABLE llx_projet_task_add ADD unit_budget INTEGER NULL DEFAULT '0' AFTER fk_item;



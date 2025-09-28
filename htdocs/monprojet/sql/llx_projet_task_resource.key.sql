ALTER TABLE llx_projet_task_resource DROP INDEX uk_unique;
ALTER TABLE llx_projet_task_resource ADD UNIQUE uk_unique (fk_projet_task, ref);
ALTER TABLE llx_projet_task_resource CHANGE fk_product_task fk_product_projet INTEGER NOT NULL;
ALTER TABLE llx_projet_task_resource ADD fk_product_budget integer after fk_product_projet;
ALTER TABLE llx_projet_task_resource ADD ref_ext varchar(30) after ref;
ALTER TABLE llx_projet_task_resource ADD group_resource VARCHAR(2) NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_projet_task_resource ADD type_resource VARCHAR(6) NOT NULL AFTER group_resource;
ALTER TABLE llx_projet_task_resource ADD fk_objectdet INTEGER NULL AFTER object;
ALTER TABLE llx_projet_task_resource ADD objectdet VARCHAR(100) NULL AFTER fk_objectdet;
ALTER TABLE llx_projet_task_resource ADD date_resource DATE NOT NULL AFTER ref_ext;
ALTER TABLE llx_projet_task_resource ADD fk_facture_fourn INTEGER NULL DEFAULT '0' AFTER fk_product_budget;
ALTER TABLE llx_projet_task_resource ADD fk_projet_paiement INTEGER NULL DEFAULT '0' AFTER fk_facture_fourn;
ALTER TABLE llx_projet_task_resource ADD fk_stock_mouvement INTEGER NULL DEFAULT '0' AFTER objectdet;


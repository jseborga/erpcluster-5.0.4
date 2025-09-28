ALTER TABLE llx_sol_almacendet ADD UNIQUE INDEX uk_fkalmacen_fk_product (fk_almacen,fk_product);
ALTER TABLE llx_sol_almacendet DROP INDEX uk_fkalmacen_fk_product;

ALTER TABLE llx_sol_almacendet ADD price DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER qty_livree;
ALTER TABLE llx_sol_almacendet ADD fk_fabricationdet INTEGER NULL DEFAULT '0' AFTER fk_product;
ALTER TABLE llx_sol_almacendet ADD description TEXT NULL AFTER fk_fabricationdet;
ALTER TABLE llx_sol_almacendet ADD fk_projet INTEGER NULL AFTER fk_fabricationdet;
ALTER TABLE llx_sol_almacendet ADD fk_projet_task INTEGER NULL AFTER fk_projet;
ALTER TABLE llx_sol_almacendet ADD fk_user_create INTEGER NOT NULL AFTER date_shipping;
ALTER TABLE llx_sol_almacendet ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_sol_almacendet ADD date_create DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_sol_almacendet ADD date_mod DATE NULL AFTER date_create;
ALTER TABLE llx_sol_almacendet ADD tms TIMESTAMP NOT NULL AFTER date_mod;
ALTER TABLE llx_sol_almacendet ADD status TINYINT NOT NULL AFTER tms;
ALTER TABLE llx_sol_almacendet DROP fk_date_create;
ALTER TABLE llx_sol_almacendet DROP fk_date_mod;

ALTER TABLE llx_sol_almacendet ADD price_peps DOUBLE NULL DEFAULT '0' AFTER price;
ALTER TABLE llx_sol_almacendet ADD price_ueps DOUBLE NULL DEFAULT '0' AFTER price_peps;

ALTER TABLE llx_sol_almacendet ADD fk_jobs INTEGER NULL DEFAULT '0' AFTER fk_projet_task;
ALTER TABLE llx_sol_almacendet ADD fk_jobsdet INTEGER NULL DEFAULT '0' AFTER fk_jobs;
ALTER TABLE llx_sol_almacendet ADD fk_structure INTEGER NULL DEFAULT '0' AFTER fk_jobsdet;
ALTER TABLE llx_sol_almacendet ADD fk_entrepot INTEGER NULL DEFAULT NULL AFTER fk_structure;
ALTER TABLE llx_sol_almacendet ADD fk_unit INTEGER NULL DEFAULT NULL AFTER fk_product;
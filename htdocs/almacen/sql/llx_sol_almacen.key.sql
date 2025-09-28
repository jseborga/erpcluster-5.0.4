ALTER TABLE llx_sol_almacen ADD UNIQUE uk_unique (entity, ref);

ALTER TABLE llx_sol_almacen ADD fk_user INTEGER NOT NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_sol_almacen ADD model_pdf VARCHAR(50) NULL AFTER description;
ALTER TABLE llx_sol_almacen ADD fk_entrepot_from INTEGER NOT NULL DEFAULT '0' AFTER ref;
ALTER TABLE llx_sol_almacen ADD fk_user_create INTEGER NOT NULL AFTER model_pdf;
ALTER TABLE llx_sol_almacen ADD fk_user_mod INTEGER NULL AFTER fk_user_create;
ALTER TABLE llx_sol_almacen ADD tms timestamp AFTER fk_user_mod;
ALTER TABLE llx_sol_almacen CHANGE date_creation date_creation DATETIME NOT NULL;
ALTER TABLE llx_sol_almacen CHANGE date_delivery date_delivery DATETIME NOT NULL;
ALTER TABLE llx_sol_almacen ADD fk_projet INTEGER NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_sol_almacen ADD fk_departament INTEGER NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_sol_almacen CHANGE date_delivery date_delivery DATETIME NULL;
ALTER TABLE llx_sol_almacen ADD fk_user_app INTEGER NULL DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_sol_almacen ADD fk_user_ent INTEGER NULL AFTER fk_user_app;
ALTER TABLE llx_sol_almacen ADD datea DATE NULL AFTER fk_user_ent;
ALTER TABLE llx_sol_almacen ADD datee DATE NULL AFTER datea;
ALTER TABLE llx_sol_almacen ADD datem DATE NULL AFTER datee;
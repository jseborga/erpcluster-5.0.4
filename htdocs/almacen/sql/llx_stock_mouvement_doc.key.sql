ALTER TABLE llx_stock_mouvement_doc ADD UNIQUE uk_unique (entity, ref);
ALTER TABLE llx_stock_mouvement_doc CHANGE model_doc model_pdf VARCHAR( 100 ) NULL DEFAULT NULL ;
ALTER TABLE llx_stock_mouvement_doc ADD fk_departament INTEGER DEFAULT NULL AFTER fk_entrepot_to;
ALTER TABLE llx_stock_mouvement_doc ADD fk_soc INTEGER DEFAULT NULL AFTER fk_departament;
ALTER TABLE llx_stock_mouvement_doc ADD ref_ext VARCHAR(30) NULL AFTER ref;
ALTER TABLE llx_stock_mouvement_doc ADD fk_type_mov integer NULL AFTER fk_soc;
ALTER TABLE llx_stock_mouvement_doc ADD fk_source INTEGER NULL DEFAULT '0' AFTER fk_type_mov;
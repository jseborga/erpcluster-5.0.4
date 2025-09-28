ALTER TABLE llx_assets ADD UNIQUE KEY uk_unique (entity,ref);
ALTER TABLE llx_assets ADD coste DOUBLE(24,5) NULL DEFAULT '0' AFTER quant;
ALTER TABLE llx_assets ADD fk_father integer NULL DEFAULT '0' AFTER entity;
ALTER TABLE llx_assets ADD been tinyint NULL DEFAULT '1' AFTER mark;
ALTER TABLE llx_assets CHANGE descrip descrip VARCHAR(250) NOT NULL;
ALTER TABLE llx_assets ADD useful_life DOUBLE NULL AFTER fk_product;
ALTER TABLE llx_assets ADD fk_unit INTEGER NULL AFTER useful_life;
ALTER TABLE llx_assets ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_assets ADD date_mod date NOT NULL AFTER date_create;
ALTER TABLE llx_assets ADD date_active DATE NULL AFTER date_adq;
ALTER TABLE llx_assets ADD coste_residual DOUBLE(24,5) NOT NULL AFTER coste;
ALTER TABLE llx_assets ADD percent DOUBLE NULL AFTER useful_life;
ALTER TABLE llx_assets ADD account_accounting VARCHAR( 30 ) NULL AFTER percent;
ALTER TABLE llx_assets ADD fk_facture_fourn INTEGER NULL AFTER fk_father;
ALTER TABLE llx_assets ADD fk_facture INTEGER NULL DEFAULT '0' AFTER fk_facture_fourn;
ALTER TABLE llx_assets ADD model_pdf VARCHAR( 50 ) NULL AFTER fk_unit ;
ALTER TABLE llx_assets ADD coste_unit_use DOUBLE NULL DEFAULT '0' AFTER model_pdf;
ALTER TABLE llx_assets ADD fk_unit_use INTEGER NULL DEFAULT '0' AFTER coste_unit_use;
ALTER TABLE llx_assets CHANGE been been VARCHAR(30) NULL DEFAULT '1';

ALTER TABLE llx_c_assets ADD INDEX idx_type_group (type_group);
ALTER TABLE llx_c_assets ADD INDEX idx_type_patrim (type_patrim);
ALTER TABLE llx_c_assets ADD INDEX idx_been (been);

ALTER TABLE llx_assets ADD CONSTRAINT llx_assets_ibfk_1 FOREIGN KEY (type_group) REFERENCES llx_c_assets_group (code) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE llx_assets ADD CONSTRAINT llx_assets_ibfk_2 FOREIGN KEY (type_patrim) REFERENCES llx_c_assets_patrim (code) ON DELETE RESTRICT ON UPDATE RESTRICT ;
ALTER TABLE llx_assets ADD CONSTRAINT llx_assets_ibfk_3 FOREIGN KEY (been) REFERENCES llx_c_assets_been (code) ON DELETE RESTRICT ON UPDATE RESTRICT ;

ALTER TABLE llx_assets DROP FOREIGN KEY llx_assets_ibfk_2;
ALTER TABLE llx_assets ADD CONSTRAINT llx_assets_ibfk_2 FOREIGN KEY (type_patrim) REFERENCES llx_c_assets_patrim(code) ON DELETE RESTRICT ON UPDATE CASCADE;


ALTER TABLE llx_assets ADD fk_asset_mov INTEGER NULL DEFAULT '0' AFTER tms;
ALTER TABLE llx_assets ADD status_reval TINYINT DEFAULT NULL  AFTER fk_asset_mov;

ALTER TABLE llx_assets ADD date_reval DATE NULL AFTER date_active;
ALTER TABLE llx_assets ADD useful_life_residual INTEGER NOT NULL AFTER date_reval;
ALTER TABLE llx_assets ADD coste_reval DOUBLE(24,5) NULL AFTER coste_residual;
ALTER TABLE llx_assets ADD coste_residual_reval DOUBLE(24,5) NULL AFTER coste_reval;
ALTER TABLE llx_assets ADD amount_sale DOUBLE(24,5) DEFAULT NULL AFTER date_baja;

ALTER TABLE llx_assets ADD ref_ext VARCHAR(50) NULL DEFAULT NULL AFTER ref;
ALTER TABLE llx_assets ADD codcont INTEGER NULL DEFAULT NULL AFTER fk_unit_use;
ALTER TABLE llx_assets ADD codaux INT NULL DEFAULT NULL AFTER codcont;
ALTER TABLE llx_assets ADD dep_acum DOUBLE NULL DEFAULT '0' NULL AFTER coste_residual_reval;
ALTER TABLE llx_assets ADD date_day TINYINT NOT NULL AFTER date_adq;
ALTER TABLE llx_assets ADD date_month TINYINT NOT NULL AFTER date_day;
ALTER TABLE llx_assets ADD date_year MEDIUMINT NOT NULL AFTER date_month;
ALTER TABLE llx_assets ADD orgfin VARCHAR(3) NULL DEFAULT NULL AFTER codaux;
ALTER TABLE llx_assets ADD cod_rube VARCHAR(15) NULL DEFAULT NULL AFTER orgfin;
ALTER TABLE llx_assets ADD fk_departament INTEGER NULL DEFAULT NULL AFTER cod_rube;
ALTER TABLE llx_assets ADD fk_resp INTEGER NULL DEFAULT NULL AFTER fk_departament;

ALTER TABLE llx_assets ADD departament_name VARCHAR(200) NULL DEFAULT NULL AFTER fk_resp;
ALTER TABLE llx_assets ADD resp_name VARCHAR(150) NULL DEFAULT NULL AFTER departament_name;
ALTER TABLE llx_assets ADD useful_life_reval DOUBLE NULL DEFAULT NULL AFTER coste_residual_reval;
ALTER TABLE llx_assets ADD fk_low INT NULL DEFAULT NULL AFTER dep_acum;
ALTER TABLE llx_assets ADD baja_day TINYINT NULL DEFAULT NULL AFTER date_baja;
ALTER TABLE llx_assets ADD baja_month TINYINT NULL DEFAULT NULL AFTER baja_day;
ALTER TABLE llx_assets ADD baja_year MEDIUMINT NULL DEFAULT NULL AFTER baja_month;
ALTER TABLE llx_assets ADD baja_resolution VARCHAR(16) NULL DEFAULT NULL AFTER baja_year;
ALTER TABLE llx_assets ADD baja_observation TEXT NULL DEFAULT NULL AFTER baja_resolution;
ALTER TABLE llx_assets ADD baja_motive VARCHAR(120) NULL DEFAULT NULL AFTER baja_observation;
ALTER TABLE llx_assets ADD baja_amount_act DOUBLE(24,5) NULL DEFAULT NULL AFTER baja_motive;
ALTER TABLE llx_assets ADD baja_amount_depgestion DOUBLE(24,5) NULL DEFAULT NULL AFTER baja_amount_act;

ALTER TABLE llx_assets ADD useful_life_two DOUBLE NULL DEFAULT NULL AFTER useful_life;
ALTER TABLE llx_assets ADD fk_unit_useful_life_two TINYINT NULL DEFAULT NULL AFTER useful_life_two;
ALTER TABLE llx_c_flow_models ADD UNIQUE KEY uk_cflowmodels_entity_group_code (entity,groups,code);
ALTER TABLE llx_c_flow_models ADD UNIQUE KEY uk_cflowmodels_entity_group_sequen (entity,groups,sequen);
ALTER TABLE llx_c_flow_models CHANGE code_area_last code_actor_last VARCHAR(30) NOT NULL;
ALTER TABLE llx_c_flow_models CHANGE code_area_next code_actor_next VARCHAR(30) NULL;
ALTER TABLE llx_c_flow_models ADD code_actor_last varchar(30) NOT NULL AFTER code;
ALTER TABLE llx_c_flow_models ADD code_actor_next varchar(30) NOT NULL AFTER code_area_last;
ALTER TABLE llx_c_flow_models ADD deadlines TINYINT NOT NULL DEFAULT '0' AFTER code_area_next;

ALTER TABLE llx_c_flow_models ADD label1 VARCHAR(255) NULL AFTER label;
ALTER TABLE llx_c_flow_models ADD label2 VARCHAR(255) NULL AFTER label1;
ALTER TABLE llx_c_flow_models ADD label3 VARCHAR(255) NULL AFTER label2;
ALTER TABLE llx_c_flow_models ADD label4 VARCHAR(255) NULL AFTER label3;

ALTER TABLE llx_c_flow_models ADD code0 VARCHAR(30) NULL AFTER code;
ALTER TABLE llx_c_flow_models ADD code1 VARCHAR(30) NULL AFTER code0;
ALTER TABLE llx_c_flow_models ADD code2 VARCHAR(30) NULL AFTER code1;
ALTER TABLE llx_c_flow_models ADD code3 VARCHAR(30) NULL AFTER code2;
ALTER TABLE llx_c_flow_models ADD code4 VARCHAR(30) NULL AFTER code3;

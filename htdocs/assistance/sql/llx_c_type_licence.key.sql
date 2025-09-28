ALTER TABLE llx_c_type_licence ADD UNIQUE uk_unique (entity, code);
ALTER TABLE llx_c_type_licence ADD type VARCHAR(1) NOT NULL AFTER label;
ALTER TABLE llx_c_type_licence ADD limited_time TINYINT NOT NULL DEFAULT '0' AFTER type;
ALTER TABLE llx_c_type_licence ADD fk_user_create INTEGER NOT NULL AFTER active;
ALTER TABLE llx_c_type_licence ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_c_type_licence ADD datec DATE NULL DEFAULT NULL AFTER fk_user_mod;
ALTER TABLE llx_c_type_licence ADD datem DATE NULL DEFAULT NULL AFTER datec;
ALTER TABLE llx_c_type_licence ADD tms TIMESTAMP NOT NULL AFTER datem;
ALTER TABLE llx_c_type_licence ADD status TINYINT DEFAULT '1' NOT NULL AFTER tms;

ALTER TABLE llx_c_type_licence ADD type_limited TINYINT NOT NULL DEFAULT '0' AFTER limited_time;
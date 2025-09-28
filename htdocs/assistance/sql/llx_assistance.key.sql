ALTER TABLE llx_assistance ADD fk_soc integer DEFAULT '0' NULL;
ALTER TABLE llx_assistance ADD images varchar(150) NULL;

ALTER TABLE llx_assistance ADD UNIQUE KEY uk_assistence_unique (entity,fk_soc,fk_member,code_activitie);
ALTER TABLE llx_assistance ADD active TINYINT NOT NULL DEFAULT '1' AFTER images;
ALTER TABLE llx_assistance ADD fk_licence INTEGER NULL DEFAULT NULL AFTER fk_member;
ALTER TABLE llx_assistance CHANGE date_create datec DATE NOT NULL;
ALTER TABLE llx_assistance ADD datem DATE NULL DEFAULT NULL AFTER datec;
ALTER TABLE llx_assistance ADD backwardness INTEGER NULL AFTER date_ass;
ALTER TABLE llx_assistance ADD abandonment INTEGER NULL AFTER backwardness;
ALTER TABLE llx_assistance ADD manual_reg TINYINT NULL DEFAULT NULL AFTER active;


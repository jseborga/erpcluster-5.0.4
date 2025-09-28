ALTER TABLE llx_type_marking ADD UNIQUE KEY uk_assistence_unique(entity,ref);
ALTER TABLE llx_type_marking ADD fixed_date DATE NULL AFTER mark;
ALTER TABLE llx_type_marking ADD INDEX idx_fk_user_create (fk_user_create);
ALTER TABLE llx_type_marking ADD sex VARCHAR(2) NOT NULL DEFAULT '-1' AFTER fixed_date;

ALTER TABLE llx_type_marking ADD FOREIGN KEY (fk_user_create) REFERENCES llx_user(rowid) ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE llx_type_marking ADD day_def VARCHAR(13) NULL DEFAULT '0,1,2,3,4,5,6' AFTER sex;
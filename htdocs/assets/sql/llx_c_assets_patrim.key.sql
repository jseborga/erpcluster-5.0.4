ALTER TABLE llx_c_assets_patrim ADD entity INTEGER NOT NULL AFTER rowid;
ALTER TABLE llx_c_assets_patrim ADD UNIQUE uk_unique (entity,code);
ALTER TABLE llx_c_assets_patrim ADD INDEX idx_code (code);

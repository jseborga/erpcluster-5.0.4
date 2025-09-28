ALTER TABLE llx_c_assets_been ADD UNIQUE uk_unique (entity,code);
ALTER TABLE llx_c_assets_been ADD INDEX idx_code (code);

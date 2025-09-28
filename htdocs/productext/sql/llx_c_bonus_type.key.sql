ALTER TABLE llx_c_bonus_type ADD UNIQUE KEY uk_unique (entity,ref);
ALTER TABLE llx_c_bonus_type ADD INDEX idx_ref (ref);
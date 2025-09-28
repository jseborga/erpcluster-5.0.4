ALTER TABLE llx_c_price_level DROP INDEX uk_c_typent;
ALTER TABLE llx_c_price_level ADD UNIQUE uk_c_unique (entity, code);
ALTER TABLE llx_c_price_level ADD nlevel integer NOT NULL after entity;
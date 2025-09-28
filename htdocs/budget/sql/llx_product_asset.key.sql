ALTER TABLE llx_product_asset ADD UNIQUE uk_unique (fk_product);
ALTER TABLE llx_product_asset CHANGE diesel_lubicants diesel_lubricants double(24,8);
ALTER TABLE llx_product_asset ADD type varchar(150) DEFAULT NULL AFTER formula;
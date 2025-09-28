ALTER TABLE llx_assets_condition ADD KEY uk_unique(fk_asset,ref,dater);
ALTER TABLE llx_assets_condition ADD CONSTRAINT idk_assetscondition_fk_asset FOREIGN KEY (fk_asset) REFERENCES llx_assets(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE llx_assets_condition CHANGE ref ref VARCHAR(30) NOT NULL;
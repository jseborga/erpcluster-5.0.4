ALTER TABLE llx_assets_assignment_det ADD UNIQUE uk_unique (fk_asset_assignment, fk_asset);
ALTER TABLE llx_assets_assignment_det ADD CONSTRAINT idk_assetsassignmentdet_fk_asset_assignment FOREIGN KEY (fk_asset_assignment) REFERENCES llx_assets_assignment(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE llx_assets_assignment_det ADD CONSTRAINT idk_assetsassignmentdet_fk_asset FOREIGN KEY (fk_asset) REFERENCES llx_assets(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_assets_assignment_det ADD fk_user_mod INTEGER NOT NULL DEFAULT '0' AFTER fk_user_create;
ALTER TABLE llx_assets_assignment_det ADD date_mod DATE NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_assets_assignment_det ADD active TINYINT NOT NULL DEFAULT '0' AFTER tms;
ALTER TABLE llx_assets_assignment_det ADD detail VARCHAR(100) NULL AFTER date_mod;
ALTER TABLE llx_assets_assignment_det ADD been TINYINT NULL AFTER detail;
ALTER TABLE llx_assets_assignment_det CHANGE statut status TINYINT NOT NULL;
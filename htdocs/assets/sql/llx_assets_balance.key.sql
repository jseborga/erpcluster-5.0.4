ALTER TABLE llx_assets_balance ADD UNIQUE uk_unique ( fk_asset );
ALTER TABLE llx_assets_balance ADD CONSTRAINT idk_assetsbalance_fk_asset FOREIGN KEY (fk_asset) REFERENCES llx_assets(rowid) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE llx_assets_balance ADD type_group varchar(30) NOT NULL AFTER ref;
ALTER TABLE llx_assets_balance ADD amount_base DOUBLE(24,5) NOT NULL DEFAULT '0' AFTER coste_residual;
ALTER TABLE llx_assets_balance ADD amount_sale DOUBLE(24,5) NULL AFTER amount_balance_depr;

ALTER TABLE llx_assets_balance ADD date_ini_day TINYINT NULL DEFAULT NULL AFTER date_end;
ALTER TABLE llx_assets_balance ADD date_ini_month TINYINT NULL DEFAULT NULL AFTER date_ini_day;
ALTER TABLE llx_assets_balance ADD date_ini_year MEDIUMINT NULL DEFAULT NULL AFTER date_ini_month;
ALTER TABLE llx_assets_balance ADD date_mig_day TINYINT NULL DEFAULT NULL AFTER date_ini_year;
ALTER TABLE llx_assets_balance ADD date_mig_month TINYINT NULL DEFAULT NULL AFTER date_mig_day;
ALTER TABLE llx_assets_balance ADD date_mig_year MEDIUMINT NULL DEFAULT NULL AFTER date_mig_month;
ALTER TABLE llx_assets_balance ADD date_mig DATE NULL DEFAULT NULL AFTER date_mig_year;
ALTER TABLE llx_assets_balance ADD coste_mig DOUBLE(24,5) NULL DEFAULT '0' AFTER coste;

ALTER TABLE llx_assets_balance ADD detail TEXT NULL AFTER movement_type;
ALTER TABLE llx_assets_balance ADD date_adq DATE NULL AFTER month_depr;
ALTER TABLE llx_assets_balance ADD date_reval_day TINYINT NULL DEFAULT NULL AFTER detail;
ALTER TABLE llx_assets_balance ADD date_reval_month TINYINT NULL DEFAULT NULL AFTER date_reval_day;
ALTER TABLE llx_assets_balance ADD date_reval_year MEDIUMINT NULL DEFAULT NULL AFTER date_reval_month;
ALTER TABLE llx_assets_balance ADD date_reval DATE NULL DEFAULT NULL AFTER date_reval_year;
ALTER TABLE llx_assets_balance ADD useful_life DOUBLE NULL DEFAULT NULL AFTER date_adq;
ALTER TABLE llx_assets_balance ADD doc_reval VARCHAR(30) NULL DEFAULT NULL AFTER movement_type;
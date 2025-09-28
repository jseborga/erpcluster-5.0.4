ALTER TABLE llx_stock_mouvement_pricemod ADD UNIQUE KEY uk_unique (fk_stock_mouvement,period_year,month_year);
ALTER TABLE llx_stock_mouvement_pricemod ADD price DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER qty;
ALTER TABLE llx_stock_mouvement_pricemod ADD price_new DOUBLE NOT NULL DEFAULT '0' AFTER balance_ueps_new;
ALTER TABLE llx_stock_mouvement_pricemod ADD date_closed DATE NULL DEFAULT NULL AFTER month_year;
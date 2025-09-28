ALTER TABLE llx_sol_almacendet_fabrication ADD UNIQUE uk_unique (fk_almacendet,fk_fabricationdet);
ALTER TABLE llx_sol_almacendet_fabrication ADD qty_livree DOUBLE(24) NULL AFTER qty;

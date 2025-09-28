ALTER TABLE llx_fabrication ADD UNIQUE uk_unique (entity, ref);
ALTER TABLE llx_fabrication ADD date_init DATETIME NULL AFTER date_delivery;
ALTER TABLE llx_fabrication ADD date_finish DATETIME NULL AFTER date_init;

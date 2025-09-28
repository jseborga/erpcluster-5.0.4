ALTER TABLE `llx_product_list` ADD UNIQUE INDEX uk_product_list_fk_product_father_son (`fk_product_father`,`fk_product_son`);

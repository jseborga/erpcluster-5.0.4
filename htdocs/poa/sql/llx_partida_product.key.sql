ALTER TABLE llx_partida_product ADD PRIMARY KEY (code_partida,fk_product);
ALTER TABLE llx_partida_product ADD KEY idx_product_code_partida (code_partida);
ALTER TABLE llx_partida_product ADD KEY idx_code_partida_product (fk_product);
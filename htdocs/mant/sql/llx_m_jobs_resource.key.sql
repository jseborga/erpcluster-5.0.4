ALTER TABLE llx_m_jobs_resource ADD PRIMARY KEY (rowid);
ALTER TABLE llx_m_jobs_resource ADD UNIQUE KEY uk_unique (fk_jobs,ref);
ALTER TABLE llx_m_jobs_resource ADD KEY idx_fk_sol_almacen (fk_sol_almacen);
ALTER TABLE llx_m_jobs_resource ADD KEY idx_fk_sol_almacendet (fk_sol_almacendet);
ALTER TABLE llx_m_jobs_resource ADD KEY idx_fk_product (fk_product);
ALTER TABLE llx_m_jobs_resource ADD fk_jobs_program INTEGER NULL AFTER fk_sol_almacendet;

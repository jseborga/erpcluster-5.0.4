ALTER TABLE llx_m_jobs_order ADD UNIQUE uk_mjobsorder_fkjobs_order (fk_jobs,order);
ALTER TABLE llx_m_jobs_order ADD fk_product INT NOT NULL AFTER fk_jobs;
ALTER TABLE llx_m_jobs_order ADD quant REAL NOT NULL AFTER description;
ALTER TABLE llx_m_jobs_order ADD unit VARCHAR(20) NULL AFTER quant;

ALTER TABLE llx_m_jobs_advance ADD UNIQUE uk_unique(fk_jobs,ref);
ALTER TABLE llx_m_jobs_advance ADD fk_speciality INTEGER NOT NULL AFTER fk_jobs_program;

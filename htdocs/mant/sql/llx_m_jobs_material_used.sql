CREATE TABLE llx_m_jobs_material_used (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_jobs integer NOT NULL,
  ref varchar(15) NOT NULL,
  date_return date NOT NULL,
  description varchar(200) NOT NULL,
  quant double NOT NULL,
  unit varchar(20) NOT NULL,
  tms timestamp NOT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

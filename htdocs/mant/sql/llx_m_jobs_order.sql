CREATE TABLE llx_m_jobs_order (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_jobs integer NOT NULL,
  fk_product integer NOT NULL,
  order_number varchar(15) NOT NULL,
  date_order date DEFAULT NULL,
  description text NULL,
  quant REAL NOT NULL,
  unit VARCHAR(20) NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

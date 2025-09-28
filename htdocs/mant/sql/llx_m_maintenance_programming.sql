CREATE TABLE llx_m_maintenance_programming (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_equipment integer NOT NULL,
  code_mant varchar(30) NOT NULL,
  date_create date NOT NULL,
  date_ini date NOT NULL,
  frequency double NOT NULL,
  type_frequency varchar(1) NOT NULL,
  insp_book tinyint DEFAULT '0',
  code_insp_book varchar(30) DEFAULT NULL,
  date_insp_last date DEFAULT NULL,
  date_insp_next date DEFAULT NULL,
  create_work_order tinyint DEFAULT '0',
  create_email tinyint DEFAULT '0',
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

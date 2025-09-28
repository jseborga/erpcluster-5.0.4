CREATE TABLE llx_projet_task_time_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_task_time integer NOT NULL,
  document varchar(100) COLLATE utf8_bin DEFAULT NULL,
  unit_declared double(24,3) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

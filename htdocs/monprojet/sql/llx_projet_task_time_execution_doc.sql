CREATE TABLE llx_projet_task_time_execution_doc (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_task_time integer NOT NULL,
  fk_task_payment integer DEFAULT '0',
  fk_request_item integer DEFAULT '0',
  fk_soc integer NOT NULL,
  document varchar(255) DEFAULT NULL,
  unit_declared double(24,3) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  fk_user_mod integer DEFAULT '0',
  tms timestamp,
  date_mod date DEFAULT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;
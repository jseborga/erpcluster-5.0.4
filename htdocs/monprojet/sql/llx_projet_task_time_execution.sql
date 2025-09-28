CREATE TABLE llx_projet_task_time_execution (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_task integer NOT NULL,
  task_date date DEFAULT NULL,
  task_datehour datetime DEFAULT NULL,
  task_date_withhour integer DEFAULT '0',
  task_duration double DEFAULT NULL,
  fk_user integer DEFAULT NULL,
  thm double(24,8) DEFAULT NULL,
  note text,
  invoice_id integer DEFAULT NULL,
  invoice_line_id integer DEFAULT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_projet_task_time_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_task_time integer NOT NULL,
  fk_task_payment integer DEFAULT 0 NOT NULL,
  fk_request_item integer DEFAULT 0 NULL,
  document varchar(100) NOT NULL,
  unit_declared double(24,3) NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NULL,
  date_create date NOT NULL,
  date_mod date NULL,	      
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

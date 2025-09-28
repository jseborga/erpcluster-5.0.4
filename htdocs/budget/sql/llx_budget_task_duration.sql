CREATE TABLE llx_budget_task_duration (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_budget_task integer NOT NULL,
  sucessor text NULL,
  predecessor text NULL,
  duration integer NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer DEFAULT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
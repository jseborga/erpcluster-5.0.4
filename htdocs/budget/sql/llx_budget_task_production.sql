CREATE TABLE llx_budget_task_production (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_budget_task integer NOT NULL,
  fk_variable integer NOT NULL,
  fk_budget_task_product integer NOT NULL,
  quantity double(24,8) NOT NULL,
  active tinyint(4) NOT NULL DEFAULT '1',
  fk_object integer DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
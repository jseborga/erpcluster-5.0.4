CREATE TABLE llx_budget_task_productivity (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_budget_task_resource integer NOT NULL,
  code_parameter varchar(2) NOT NULL,
  quant double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
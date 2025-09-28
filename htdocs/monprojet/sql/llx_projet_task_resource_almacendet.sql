CREATE TABLE llx_projet_task_resource_almacendet (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_projet_task integer NOT NULL,
  fk_projet_task_resource integer NOT NULL,
  fk_sol_almacen_det integer NOT NULL,
  fk_product integer NOT NULL,
  quant double NOT NULL,
  subprice double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
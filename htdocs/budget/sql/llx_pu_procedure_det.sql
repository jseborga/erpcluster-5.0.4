CREATE TABLE llx_pu_procedure_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_pu_procedure integer NOT NULL,
  sequen integer NOT NULL,
  ref_procedure varchar(30) NOT NULL,
  detail varchar(50) NOT NULL,
  formula varchar(10) NOT NULL,
  status_print tinyint DEFAULT '0' NOT NULL,
  status_print_det tinyint DEFAULT '1' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
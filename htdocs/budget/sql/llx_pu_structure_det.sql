CREATE TABLE llx_pu_structure_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref_structure varchar(30) NOT NULL,
  type_structure varchar(30) NOT NULL,
  sequen integer NOT NULL,
  detail varchar(100) NOT NULL,
  formula varchar(10) NOT NULL,
  status_print varchar(1) DEFAULT '0' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

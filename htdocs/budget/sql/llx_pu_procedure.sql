CREATE TABLE llx_pu_procedure (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  detail varchar(80) NOT NULL,
  description text,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
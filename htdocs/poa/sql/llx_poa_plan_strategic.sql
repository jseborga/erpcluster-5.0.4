CREATE TABLE llx_poa_plan_strategic (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  year_ini mediumint NOT NULL,
  year_fin mediumint NOT NULL,
  label varchar(255) NOT NULL,
  pseudonym varchar(150) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint(4) NOT NULL
) ENGINE=InnoDB;
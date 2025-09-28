CREATE TABLE llx_parameter_calculation (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(100) DEFAULT NULL,
  type varchar(30) DEFAULT NULL,
  amount double(24,5) DEFAULT '0' NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1',
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
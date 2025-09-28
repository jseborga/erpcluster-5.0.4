CREATE TABLE llx_c_type_licence (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(50) NOT NULL,
  type varchar(1) NOT NULL,
  limited_time tinyint DEFAULT '0' NOT NULL,
  type_limited tinyint DEFAULT '0' NOT NULL,
  active tinyint NOT NULL DEFAULT '1',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

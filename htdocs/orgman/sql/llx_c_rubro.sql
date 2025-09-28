CREATE TABLE llx_c_rubro (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  period_year smallint NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  detail text,
  code_father varchar(30) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint DEFAULT NULL
) ENGINE=InnoDB;

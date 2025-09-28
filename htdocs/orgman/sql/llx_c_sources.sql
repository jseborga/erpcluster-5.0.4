CREATE TABLE llx_c_sources (
  rowid INTEGER PRIMARY KEY AUTO_INCREMENT,
  entity integer NOT NULL DEFAULT '1',
  code varchar(30) NOT NULL,
  label varchar(60) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

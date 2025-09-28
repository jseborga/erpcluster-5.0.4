CREATE TABLE llx_tables_def (
  rowid integer NOT NULL,
  entity integer NOT NULL,
  ref varchar(80) NOT NULL,
  label varchar(150) NOT NULL,
  with_limit tinyint NOT NULL DEFAULT '0',
  active tinyint NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
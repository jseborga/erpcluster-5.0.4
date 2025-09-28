CREATE TABLE llx_tables_def_det (
  rowid integer NOT NULL,
  fk_table_def integer NOT NULL DEFAULT '1',
  ref varchar(30) NOT NULL,
  label varchar(200) NOT NULL,
  description text,
  range_ini integer DEFAULT NULL,
  range_fin integer DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
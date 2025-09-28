CREATE TABLE llx_p_period (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_proces integer NOT NULL,
  fk_type_fol integer NOT NULL,
  ref varchar( 6 ) NOT NULL,
  mes smallint NOT NULL,
  anio smallint NOT NULL,
  model_pdf varchar(50) NULL,
  date_ini date NOT NULL,
  date_fin date NOT NULL,
  date_pay date NOT NULL,
  date_court date NULL,
  date_close date NULL,
  status_app tinyint DEFAULT 0 NULL,
  state smallint NOT NULL
) ENGINE=innodb;

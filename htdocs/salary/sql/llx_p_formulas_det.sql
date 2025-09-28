CREATE TABLE llx_p_formulas_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref_formula varchar(4) NOT NULL,
  fk_operator integer NOT NULL,
  type varchar( 60 ) NOT NULL,
  changefull varchar( 30 ) NOT NULL,
  nmonth tinyint NULL,
  andor smallint NOT NULL,
  sequen integer NOT NULL,
  state smallint NOT NULL
) ENGINE=innodb;

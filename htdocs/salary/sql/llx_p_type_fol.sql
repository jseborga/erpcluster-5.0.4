CREATE TABLE llx_p_type_fol (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar( 4 ) NOT NULL,
  detail varchar( 40 ) NOT NULL,
  details text,
  name_report text NULL,
  state smallint NOT NULL
) ENGINE=InnoDB;

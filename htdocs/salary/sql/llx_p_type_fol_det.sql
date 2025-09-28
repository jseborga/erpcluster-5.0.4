CREATE TABLE llx_p_type_fol_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_type_fol integer NOT NULL,
  sequen integer NOT NULL,
  detail varchar( 50 ) NOT NULL,
  formula varchar( 10 ) NOT NULL,
  state smallint NOT NULL,
  details text
) ENGINE=InnoDB;

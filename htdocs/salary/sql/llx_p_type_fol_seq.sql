CREATE TABLE llx_p_type_fol_seq (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_type_fol integer NOT NULL,
  sequen integer NOT NULL,
  ref_concept varchar(30) NOT NULL,
  details text,
  state tinyint NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'

) ENGINE=InnoDB;

CREATE TABLE llx_projet_paysoc (
  rowid integer NOT NULL AUTO_INCREMENT,
  fk_projet integer NOT NULL,
  fk_facture_fourn integer DEFAULT '0',
  ref varchar(30) NOT NULL,
  date_payment date NOT NULL,
  date_request date NOT NULL,
  amount double(24,5) DEFAULT '0.00000',
  document text,
  detail text,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  status tinyint(4) NOT NULL,
  PRIMARY KEY (rowid),
  UNIQUE KEY uk_unique (fk_projet,ref)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
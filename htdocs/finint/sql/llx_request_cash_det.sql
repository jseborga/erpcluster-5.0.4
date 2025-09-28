CREATE TABLE llx_request_cash_det (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_request integer NOT NULL,
  fk_unit integer DEFAULT '0' NOT NULL,
  quant double(18,5) DEFAULT '0' NULL,
  detail varchar(60) NOT NULL,
  amount double(24,5) NOT NULL,
  amount_approved double(24,5) DEFAULT '0' NOT NULL,
  date_create date NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_approved integer DEFAULT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1',
  status tinyint NOT NULL
) ENGINE=InnoDB;
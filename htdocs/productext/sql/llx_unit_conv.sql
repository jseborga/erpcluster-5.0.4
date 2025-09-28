CREATE TABLE llx_unit_conv (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_product integer NOT NULL,
  fk_unit_ext integer NOT NULL DEFAULT '0',
  fk_unit integer NOT NULL DEFAULT '0',
  fc double NOT NULL DEFAULT '0',
  type_fc varchar(1) DEFAULT 'M' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create datetime NOT NULL,
  date_mod datetime NOT NULL,
  tms timestamp,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_assets_assignment_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_asset_assignment integer NOT NULL,
  fk_asset integer NOT NULL,
  date_assignment date NOT NULL,
  date_end date DEFAULT NULL,
  date_create date NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_mod date NOT NULL,
  detail varchar(100) NULL,
  been tinyint NULL,
  tms timestamp,
  active tinyint DEFAULT '0' NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

CREATE TABLE llx_assets_contador (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_asset integer NOT NULL,
  period_month tinyint NOT NULL,
  period_year mediumint NOT NULL,
  quant double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
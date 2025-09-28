CREATE TABLE llx_product_pricemod (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_product integer NOT NULL,
  period_year mediumint NOT NULL,
  month_year tinyint NOT NULL,
  qty double DEFAULT NULL,
  price double NOT NULL,
  price_new double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
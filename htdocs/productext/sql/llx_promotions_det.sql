CREATE TABLE llx_promotions_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_promotions integer NOT NULL,
  fk_product integer NOT NULL,
  saleprice double NOT NULL,
  qty double NOT NULL,
  price double NOT NULL,
  subprice double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
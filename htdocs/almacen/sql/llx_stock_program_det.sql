CREATE TABLE llx_stock_program_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_stock_program integer NOT NULL,
  fk_entrepot_end integer NOT NULL,
  fk_product integer NOT NULL,
  qty double NOT NULL,
  fk_object integer DEFAULT NULL,
  object varchar(50) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec datetime NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
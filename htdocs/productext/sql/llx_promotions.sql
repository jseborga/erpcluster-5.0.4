CREATE TABLE llx_promotions (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(150) NOT NULL,
  detail text,
  fk_product integer NOT NULL,
  qty double NOT NULL,
  fk_type_promotion integer NOT NULL,
  active tinyint NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_user_val integer DEFAULT NULL,
  datec date NOT NULL,
  datem datetime NOT NULL,
  datev datetime DEFAULT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
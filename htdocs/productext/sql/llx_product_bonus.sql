CREATE TABLE llx_product_bonus (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_product integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(200) NOT NULL,
  fk_bonus_type integer NOT NULL,
  type_value varchar(1) NOT NULL,
  active tinyint NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
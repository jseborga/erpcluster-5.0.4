CREATE TABLE llx_items_product (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_item integer NOT NULL,
  ref varchar(30) NOT NULL,
  group_structure varchar(2) NOT NULL,
  fk_product integer NOT NULL,
  fk_unit integer NOT NULL,
  label varchar(255) NOT NULL,
  formula varchar(100) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_supplies (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_product_group integer NOT NULL,
  fk_item integer NOT NULL,
  fk_product integer NOT NULL,
  fk_unit integer NOT NULL,
  fk_societe integer NOT NULL,
  specificaction varchar(50) DEFAULT NULL,
  photo varchar(50) DEFAULT NULL,
  date_create date NOT NULL,
  date_delete date DEFAULT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

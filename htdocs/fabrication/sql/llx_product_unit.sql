CREATE TABLE llx_product_unit (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_product integer NOT NULL,
  fk_unit integer NOT NULL,
  active tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

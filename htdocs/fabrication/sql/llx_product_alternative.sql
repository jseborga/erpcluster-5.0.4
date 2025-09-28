CREATE TABLE llx_product_alternative (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_product integer NOT NULL,
  fk_unit integer NOT NULL,
  qty double NOT NULL,
  fk_product_alt integer NOT NULL,
  fk_unit_alt integer NOT NULL,
  qty_alt double NOT NULL,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

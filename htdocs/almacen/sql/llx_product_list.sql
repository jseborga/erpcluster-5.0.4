CREATE TABLE llx_product_list (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_product_father integer NOT NULL,
  fk_unit_father integer NOT NULL,
  fk_product_son integer NOT NULL,
  fk_unit_son integer NOT NULL,
  qty_father double(24,2) NOT NULL,
  qty_son double(24,2) NOT NULL,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

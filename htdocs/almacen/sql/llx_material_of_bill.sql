CREATE TABLE IF NOT EXISTS `llx_material_of_bill` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_product_father` int(11) NOT NULL,
  `fk_unit_father` int(11) NOT NULL,
  `fk_product_son` int(11) NOT NULL,
  `fk_unit_son` int(11) NOT NULL,
  `qty_father` double NOT NULL,
  `qty_son` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB

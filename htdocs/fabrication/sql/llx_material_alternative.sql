CREATE TABLE IF NOT EXISTS `llx_material_alternative` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL,
  `fk_unit` int(11) NOT NULL,
  `qty_base` double NOT NULL,
  `fk_product_alt` int(11) NOT NULL,
  `fk_unit_alt` int(11) NOT NULL,
  `qty_alt` double NOT NULL,
  `statut` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `llx_commande_venta` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_commande` int(11) NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabla de fabricacion' AUTO_INCREMENT=1 ;

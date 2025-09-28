CREATE TABLE IF NOT EXISTS `llx_stock_father` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `tms` datetime NOT NULL,
  `fk_entrepot` int(11) NOT NULL,
  `fk_almacen` int(11) NOT NULL DEFAULT '0',
  `fk_user_author` int(11) NOT NULL,
  `description` varchar(128) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

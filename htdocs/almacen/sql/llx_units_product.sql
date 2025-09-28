CREATE TABLE IF NOT EXISTS `llx_units_product` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `fk_product` int(11) NOT NULL,
  `fk_units` int(11) NOT NULL,
  `fk_unitsproductid` int(11) NOT NULL DEFAULT '0',
  `process` varchar(10) NOT NULL,
  `base` double NOT NULL,
  `sequence` int(2) NOT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `llx_contab_point_entry` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `ref` varchar(3) NOT NULL,
  `description` varchar(120) NOT NULL,
  `cfglan` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

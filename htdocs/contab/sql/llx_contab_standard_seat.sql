CREATE TABLE IF NOT EXISTS `llx_contab_standard_seat` (
  `rowid` int(11) NOT NULL AUTO_INCREMENT,
  `entity` int(11) NOT NULL,
  `fk_point_entry` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `description` varchar(40) NOT NULL,
  `type_seat` int(1) NOT NULL,
  `type_balance` int(1) NOT NULL,
  `debit_account` tinytext,
  `credit_account` tinytext,
  `currency` varchar(5) NOT NULL DEFAULT '11111' COMMENT 'Informa las monedas para los cuales se debe hacer los asientos contables. 1 hace el asiento en la moneda, 2 no efectua el asiento',
  `currency_value1` tinytext,
  `currency_value2` tinytext,
  `history` varchar(150) DEFAULT NULL,
  `history_group` varchar(150) DEFAULT NULL,
  `origin` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`rowid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

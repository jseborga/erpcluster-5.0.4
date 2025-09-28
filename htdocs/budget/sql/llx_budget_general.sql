CREATE TABLE llx_budget_general (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	fk_budget integer NOT NULL,
	exchange_rate double DEFAULT '1',
	base_currency varchar(30) NOT NULL,
	second_currency varchar(30) DEFAULT NULL,
	decimal_quant tinyint DEFAULT '0',
	decimal_pu tinyint DEFAULT '0',
	decimal_total tinyint DEFAULT '0',
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	datec date NOT NULL,
	datem date NOT NULL,
	tms timestamp NOT NULL,
	status tinyint(4) NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_budget_concept (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	fk_budget integer NOT NULL,
	ref varchar(30) NOT NULL,
	label varchar(100) NOT NULL,
	type varchar(30) DEFAULT NULL,
	amount_def double(24,8) DEFAULT '0' NULL,
	amount double NOT NULL,
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	date_create date NOT NULL,
	date_mod date NOT NULL,
	tms timestamp NOT NULL,
	status tinyint NOT NULL
) ENGINE=InnoDB;

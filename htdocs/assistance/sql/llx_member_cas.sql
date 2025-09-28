CREATE TABLE llx_member_cas (
	rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
	fk_member integer NOT NULL,
	dater date NOT NULL,
	number_year mediumint NOT NULL,
	number_month tinyint NOT NULL,
	number_day tinyint NOT NULL,
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	datec date NOT NULL,
	datem date NOT NULL,
	tms timestamp NOT NULL,
	status tinyint NOT NULL
) ENGINE=InnoDB;
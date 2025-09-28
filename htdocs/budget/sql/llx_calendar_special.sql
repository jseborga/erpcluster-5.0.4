CREATE TABLE llx_calendar_special (
	rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
	fk_object integer NOT NULL,
	object varchar(150) NOT NULL,
	dateo date NOT NULL,
	type_date tinyint NOT NULL,
	working_day_hour varchar(100) DEFAULT NULL,
	fk_calendar integer NULL DEFAULT '0',
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	datec date NOT NULL,
	datem date NOT NULL,
	tms timestamp NOT NULL,
	status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
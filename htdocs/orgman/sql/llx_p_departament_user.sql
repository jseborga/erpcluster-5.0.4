CREATE TABLE llx_p_departament_user (
	rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
	fk_departament integer NOT NULL,
	fk_user integer NOT NULL,
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	datec date NOT NULL,
	datem date NOT NULL,
	tms timestamp NOT NULL,
	active tinyint NOT NULL DEFAULT '0',
	privilege tinyint DEFAULT '0'
) ENGINE=InnoDB;
CREATE TABLE llx_entity_add (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	fk_entity integer NOT NULL,
	socialreason text NOT NULL,
	nit varchar(30) NOT NULL,
	activity text NULL,
	address text NULL,
	city varchar(50) NULL,
	phone varchar(12) NULL,
	message text NULL,
	status tinyint NOT NULL
) ENGINE=InnoDB;

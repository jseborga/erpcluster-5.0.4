CREATE TABLE llx_projet_add (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	fk_projet integer NOT NULL,
	fk_entrepot integer DEFAULT '0' NULL,
	programmed varchar(1) DEFAULT '0',
	fk_contracting integer DEFAULT '0',
	fk_supervising integer DEFAULT '0',
	use_resource varchar(1) NOT NULL DEFAULT '0',
	origin varchar(50) NULL,
	originid integer DEFAULT '0' NULL,
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	date_create date NOT NULL,
	date_mod date NOT NULL,
	tms timestamp NOT NULL,
	status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
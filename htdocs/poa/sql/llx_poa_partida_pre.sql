CREATE TABLE llx_poa_partida_pre (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	fk_poa_prev integer NOT NULL,
	fk_structure integer NOT NULL,
	fk_poa integer NULL,
	partida varchar(10) NOT NULL,
	amount double NOT NULL DEFAULT '0',
	fk_user_create integer NOT NULL,
	fk_user_mod integer NOT NULL,
	datec date NOT NULL,
	datem date NOT NULL,
	tms timestamp NOT NULL,
	statut tinyint NOT NULL DEFAULT '0',
	active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

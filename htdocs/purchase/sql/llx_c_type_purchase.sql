CREATE TABLE llx_c_type_purchase (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	entity integer NOT NULL,
	code varchar(12) NOT NULL,
	label varchar(50) DEFAULT NULL,
	fk_cagetorie integer DEFAULT NULL,
	active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
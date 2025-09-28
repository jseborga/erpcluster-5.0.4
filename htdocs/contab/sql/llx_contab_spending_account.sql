CREATE TABLE llx_contab_spending_account (
	rowid integer AUTO_INCREMENT PRIMARY KEY,
	entity integer NOT NULL,
	ref varchar( 12 ) NOT NULL ,
	fk_account integer NOT NULL ,
	state tinyint NOT NULL
) ENGINE = InnoDB;

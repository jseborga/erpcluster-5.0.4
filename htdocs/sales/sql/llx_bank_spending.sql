CREATE TABLE llx_bank_spending (
rowid integer AUTO_INCREMENT PRIMARY KEY,
fk_bank integer NOT NULL ,
fk_deplacement integer NOT NULL
) ENGINE = InnoDB;

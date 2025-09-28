CREATE TABLE llx_bank_historial (
rowid integer AUTO_INCREMENT PRIMARY KEY,
fk_bank_from integer NOT NULL ,
fk_bank_to integer NOT NULL ,
date_transfer DATE NOT NULL ,
fk_user_from integer NOT NULL ,
fk_user_to integer NOT NULL ,
label varchar(200) NOT NULL ,
amount DOUBLE NOT NULL ,
moneylocal TEXT NULL ,
monexext TEXT NULL ,
fk_user_create integer NOT NULL ,
date_create DATE NOT NULL ,
tms TIMESTAMP NOT NULL ,
statut TINYINT NOT NULL)
ENGINE = InnoDB;

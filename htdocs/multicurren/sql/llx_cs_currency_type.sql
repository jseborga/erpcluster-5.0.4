CREATE TABLE llx_cs_currency_type (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
entity integer NOT NULL,
ref varchar(3) NOT NULL,
label varchar(50) NOT NULL,
registry tinyint NOT NULL,
order_currency tinyint NOT NULL,
fk_user_create integer NOT NULL,
fk_user_mod integer NOT NULL,
datec date NOT NULL,
dateu date NOT NULL,
tms timestamp NOT NULL,
status tinyint NOT NULL
) ENGINE = InnoDB;

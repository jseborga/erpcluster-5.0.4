CREATE TABLE llx_cs_currency_type (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
entity integer NOT NULL,
ref varchar(30) NOT NULL,
label text NULL,
registry tinyint NOT NULL,
state tinyint NOT NULL
) ENGINE = InnoDB;

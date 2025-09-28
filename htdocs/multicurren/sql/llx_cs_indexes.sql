CREATE TABLE llx_cs_indexes (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
country integer NOT NULL,
date_ind date NOT NULL ,
currency1 double( 12, 5 ) NOT NULL ,
currency2 double( 12, 5 ) NULL DEFAULT '0.00000',
currency3 double( 12, 5 ) NULL DEFAULT '0.00000',
currency4 double( 12, 5 ) NULL DEFAULT '0.00000',
currency5 double( 12, 5 ) NULL DEFAULT '0.00000',
currency6 double( 12, 5 ) NULL DEFAULT '0.00000',
fk_user_create integer NOT NULL,
tms timestamp,
date_create date NOT NULL
) ENGINE = InnoDB;

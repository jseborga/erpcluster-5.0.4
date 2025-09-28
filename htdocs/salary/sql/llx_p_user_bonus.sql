CREATE TABLE llx_p_user_bonus (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
fk_puser integer NOT NULL ,
fk_concept integer NOT NULL ,
detail tinytext,
amount double( 15, 2 ) NOT NULL ,
type smallint NOT NULL DEFAULT '0',
date_ini date NOT NULL ,
date_fin date DEFAULT NULL ,
state smallint NOT NULL
) ENGINE = InnoDB;

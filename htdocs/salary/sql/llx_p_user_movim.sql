CREATE TABLE llx_p_user_movim (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
fk_user integer NOT NULL ,
fk_concept integer NOT NULL ,
fk_period integer NOT NULL ,
fk_type_fol integer NOT NULL ,
fk_cc integer NOT NULL ,
time_unfo double( 8, 2 ) NOT NULL ,
amount double( 20, 2 ) NOT NULL ,
amount_base double( 20, 2 ) NOT NULL ,
date_pay date NOT NULL ,
fk_user_creator integer NOT NULL ,
date_creator timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
sequen smallint NOT NULL
) ENGINE = InnoDB;

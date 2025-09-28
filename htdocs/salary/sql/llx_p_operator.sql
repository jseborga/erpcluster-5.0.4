CREATE TABLE llx_p_operator (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
detail varchar( 40 ) NOT NULL ,
operator varchar( 15 ) NOT NULL ,
type smallint NOT NULL ,
state smallint NOT NULL
) ENGINE = innodb;

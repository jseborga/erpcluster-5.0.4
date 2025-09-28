CREATE TABLE llx_p_formulas 
(
rowid integer AUTO_INCREMENT PRIMARY KEY,
entity integer DEFAULT 1 NOT NULL ,
ref varchar(4) NOT NULL ,
detail varchar(50) NOT NULL ,
state smallint NOT NULL
) ENGINE = innodb;

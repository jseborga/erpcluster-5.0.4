CREATE TABLE llx_p_charge 
(
rowid integer AUTO_INCREMENT PRIMARY KEY ,
entity integer NOT NULL ,
ref varchar( 50 ) NOT NULL ,
detail text NULL ,
skills text NULL
) ENGINE = innodb;

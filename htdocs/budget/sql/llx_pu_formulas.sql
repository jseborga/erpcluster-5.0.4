CREATE TABLE llx_pu_formulas
(
rowid integer AUTO_INCREMENT PRIMARY KEY,
entity integer DEFAULT 1 NOT NULL ,
ref varchar(4) NOT NULL ,
detail varchar(100) NOT NULL ,
statut smallint NOT NULL
) ENGINE = innodb;

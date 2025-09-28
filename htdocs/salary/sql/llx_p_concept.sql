CREATE TABLE llx_p_concept (
rowid integer AUTO_INCREMENT PRIMARY KEY ,
entity integer NOT NULL,
ref varchar( 3 ) NOT NULL ,
detail varchar( 40 ) NOT NULL ,
details text,
type_cod integer NOT NULL ,
type_mov integer DEFAULT '2',
ref_formula varchar(4) NULL,
wage_inf varchar(1) NULL,
calc_oblig smallint DEFAULT '2',
calc_afp smallint DEFAULT '2',
calc_rciva smallint DEFAULT '2',
calc_agui smallint DEFAULT '2',
calc_vac smallint DEFAULT '2',
calc_indem smallint DEFAULT '2',
calc_afpvejez smallint DEFAULT '2',
calc_contrpat smallint DEFAULT '2',
calc_afpriesgo smallint DEFAULT '2',
calc_aportsol smallint DEFAULT '2',
calc_quin smallint DEFAULT '2',
print smallint NOT NULL ,
print_input smallint NOT NULL ,
fk_codfol integer NOT NULL ,
contab_account_ref varchar(40) NULL ,
income_tax smallint NOT NULL ,
percent double( 12, 2 ) DEFAULT NULL
) ENGINE = innodb;

ALTER TABLE llx_p_concept ADD contab_account_ref varchar(40) NULL AFTER fk_codfol;
ALTER TABLE llx_p_concept ADD wage_inf varchar(1) NULL AFTER ref_formula;

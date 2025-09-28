CREATE TABLE llx_partida_product (
  code_partida varchar(30) NOT NULL,
  fk_product integer NOT NULL,
  import_key varchar(14) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
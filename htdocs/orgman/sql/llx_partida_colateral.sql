CREATE TABLE llx_partida_colateral (
  code_partida varchar(30) NOT NULL,
  code_colateral varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  percent double(6,2) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
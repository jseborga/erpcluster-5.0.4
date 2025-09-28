CREATE TABLE llx_cs_indexes_country (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(3) NOT NULL,
  date_ind date NOT NULL,
  amount double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  dateu date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
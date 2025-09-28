CREATE TABLE llx_items_formula (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_budget integer NOT NULL,
  fk_pu_structure integer NOT NULL,
  formula varchar(40) COLLATE utf8_bin NOT NULL,
  quant double NOT NULL,
  sequen integer NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create integer NOT NULL,
  date_mod integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;
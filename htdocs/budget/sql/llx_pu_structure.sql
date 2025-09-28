CREATE TABLE llx_pu_structure (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL DEFAULT '1',
  ref varchar(30) NOT NULL,
  group_structure varchar(2) NOT NULL,
  fk_user_create integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL DEFAULT '0',
  fk_categorie integer DEFAULT NULL,
  type_structure varchar(30) NOT NULL,
  complementary varchar(1) DEFAULT '1' NOT NULL,
  detail varchar(50) NOT NULL DEFAULT '',
  ordby tinyint NOT NULL DEFAULT '0',
  date_delete date DEFAULT NULL,
  date_create date NOT NULL,
  date_mod date NOT NULL,
  tms timestamp,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE llx_items_group (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  ref_ext varchar(30) DEFAULT NULL,
  version integer NOT NULL DEFAULT '1',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_parent integer DEFAULT NULL,
  type tinyint NOT NULL DEFAULT '0',
  detail varchar(100) DEFAULT '',
  fk_unit integer NOT NULL,
  fk_item integer DEFAULT '0',
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

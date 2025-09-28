CREATE TABLE llx_items_insumos (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_user_create integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL DEFAULT '0',
  fk_type integer NOT NULL,
  fk_item integer NOT NULL DEFAULT '0',
  fk_product integer NOT NULL DEFAULT '0',
  quant double NOT NULL DEFAULT '0',
  date_create date DEFAULT NULL,
  gestion year(4) NOT NULL,
  date_delete date DEFAULT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;
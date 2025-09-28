CREATE TABLE llx_product_portioning (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_product integer NOT NULL,
  fk_product_portion integer NOT NULL,
  qty double NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  active tinyint NOT NULL default '0',
  statut tinyint NOT NULL default '0'
) ENGINE=InnoDB;
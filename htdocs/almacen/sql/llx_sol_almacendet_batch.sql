CREATE TABLE llx_sol_almacendet_batch (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_solalmacendet integer NOT NULL,
  eatby date DEFAULT NULL,
  sellby date DEFAULT NULL,
  batch varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  qty double NOT NULL DEFAULT '0',
  fk_origin_stock integer NOT NULL
) ENGINE=InnoDB;
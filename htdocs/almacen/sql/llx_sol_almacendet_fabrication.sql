CREATE TABLE llx_sol_almacendet_fabrication (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_almacendet integer NOT NULL,
  fk_fabricationdet integer DEFAULT NULL,
  qty double(24,8) NOT NULL,
  qty_livree double(24,8) NULL,
  price double(24,8) DEFAULT '0.00000000'
) ENGINE=InnoDB;
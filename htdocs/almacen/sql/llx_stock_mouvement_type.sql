CREATE TABLE llx_stock_mouvement_type (
  rowid INTEGER PRIMARY KEY AUTO_INCREMENT ,
  fk_stock_mouvement INTEGER NOT NULL ,
  fk_type_mouvement INTEGER NOT NULL ,
  tms TIMESTAMP NOT NULL ,
  statut TINYINT NOT NULL
)
ENGINE = InnoDB;
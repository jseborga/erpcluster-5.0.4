CREATE TABLE llx_projet_product_association (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_projet integer NOT NULL,
  fk_product_pere integer NOT NULL DEFAULT '0',
  fk_product_fils integer NOT NULL DEFAULT '0',
  qty double DEFAULT NULL,
  incdec integer DEFAULT '1'
) ENGINE=InnoDB;
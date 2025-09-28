CREATE TABLE llx_fabricationdet (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_fabrication integer NOT NULL,
  fk_product integer NOT NULL,
  fk_commandedet integer default '0' NULL,
  qty double(24,2) NOT NULL,
  qty_decrease double(24,2) NULL,
  qty_first integer NULL,
  qty_second integer NULL,
  price double(24,8) NULL DEFAULT '0',
  price_total double(24,8) NOT NULL DEFAULT '0',
  date_end date NULL,
  date_shipping date NULL
) ENGINE=InnoDB;

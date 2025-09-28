CREATE TABLE llx_bank_categ (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL DEFAULT '1',
  label varchar(255) DEFAULT NULL
) ENGINE=InnoDB;
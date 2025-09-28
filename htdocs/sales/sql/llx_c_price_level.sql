CREATE TABLE llx_c_price_level (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  entity integer NOT NULL,
  nlevel integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(80) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

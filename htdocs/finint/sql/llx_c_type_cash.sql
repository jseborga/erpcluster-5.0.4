CREATE TABLE llx_c_type_cash (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(60) DEFAULT NULL,
  recharge tinyint DEFAULT '0' NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
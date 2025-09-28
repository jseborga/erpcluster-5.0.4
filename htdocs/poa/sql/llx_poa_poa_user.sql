CREATE TABLE llx_poa_poa_user (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_poa integer NOT NULL,
  fk_user integer NOT NULL,
  order_user tinyint NOT NULL DEFAULT '0',
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL,
  active tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

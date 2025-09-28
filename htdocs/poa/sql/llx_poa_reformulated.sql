CREATE TABLE llx_poa_reformulated (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_area integer NOT NULL,
  gestion smallint NOT NULL,
  ref tinyint NOT NULL,
  date_reform date NOT NULL,
  version tinyint NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL DEFAULT '0',
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

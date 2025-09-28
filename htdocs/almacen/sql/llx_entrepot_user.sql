CREATE TABLE llx_entrepot_user (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  fk_entrepot integer NOT NULL,
  fk_user integer NOT NULL,
  type tinyint DEFAULT '1' NOT NULL,
  typeapp tinyint DEFAULT '0' NOT NULL,
  fk_user_mod integer NOT NULL,
  date_create date NOT NULL,
  active tinyint NOT NULL DEFAULT '0',
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
CREATE TABLE llx_poa_activity_prev (
  rowid integer AUTO_INCREMENTE PRIMARY KEY,
  fk_activity integer NOT NULL DEFAULT '0',
  fk_prev integer DEFAULT '0',
  statut tinyint DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE llx_units (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL DEFAULT '0',
  fk_type integer NOT NULL DEFAULT '0',
  ref varchar(50) NOT NULL DEFAULT '',
  detail varchar(50) NOT NULL DEFAULT '',
  fk_user_create integer NOT NULL DEFAULT '0',
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=MyISAM;

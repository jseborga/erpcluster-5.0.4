CREATE TABLE llx_poa_activity_checklist (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_activity integer NOT NULL,
  code varchar(30) NOT NULL,
  checklist varchar(1) NOT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint DEFAULT '0' NULL
) ENGINE=InnoDB;

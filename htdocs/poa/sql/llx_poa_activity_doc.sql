CREATE TABLE llx_poa_activity_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_activity integer NOT NULL,
  detail varchar(50) NOT NULL,
  name_doc varchar(50) NOT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint(4) NOT NULL
) ENGINE=InnoDB;

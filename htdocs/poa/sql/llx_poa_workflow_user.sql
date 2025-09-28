CREATE TABLE llx_poa_workflow_user (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_workflow integer NOT NULL,
  code_area varchar(30) NOT NULL,
  fk_user integer NOT NULL,
  date_assign datetime NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

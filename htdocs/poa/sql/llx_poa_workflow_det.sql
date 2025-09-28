CREATE TABLE llx_poa_workflow_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_workflow integer NOT NULL,
  code_area_last varchar(30) NOT NULL,
  code_area_next varchar(30) NOT NULL,
  code_procedure varchar(30) NOT NULL,
  date_tracking datetime NOT NULL,
  date_read datetime NULL,
  detail text,
  sequen tinyint NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint(4) NOT NULL
) ENGINE=InnoDB;

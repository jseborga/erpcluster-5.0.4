CREATE TABLE llx_poa_activity_workflow (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_activity integer NOT NULL,
  code_area_last varchar(30) NULL,
  code_area_next varchar(30) NULL,
  code_procedure varchar(30) NULL,
  doc_verif text NULL,
  date_tracking datetime NOT NULL,
  followup text NOT NULL,
  followto text NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

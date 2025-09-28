CREATE TABLE llx_poa_activity_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_activity integer NOT NULL,
  code_procedure varchar(30) COLLATE utf8_bin NOT NULL,
  date_procedure date NOT NULL,
  date_create datetime NOT NULL,
  fk_user_create int(11) NOT NULL,
  tms timestamp NOT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

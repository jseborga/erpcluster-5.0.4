CREATE TABLE llx_addendum (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_contrat_father integer NOT NULL,
  fk_contrat_son integer NOT NULL,
  date_create datetime NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

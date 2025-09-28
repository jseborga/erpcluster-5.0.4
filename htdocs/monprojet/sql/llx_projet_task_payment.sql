CREATE TABLE llx_projet_task_payment (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_task integer NOT NULL,
  fk_projet_payment integer DEFAULT '0',
  document varchar(255) DEFAULT NULL,
  detail text NULL,
  unit_declared double(24,3) DEFAULT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  fk_user_mod integer DEFAULT '0',
  tms timestamp,
  date_mod date DEFAULT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

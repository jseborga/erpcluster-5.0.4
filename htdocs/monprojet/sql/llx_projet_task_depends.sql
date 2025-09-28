CREATE TABLE llx_projet_task_depends (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_task integer NOT NULL,
  fk_task_depends integer NOT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  fk_user_modif integer DEFAULT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

CREATE TABLE llx_projet_task_element (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_task integer NOT NULL,
  attachment text,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

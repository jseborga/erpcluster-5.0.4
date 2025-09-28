CREATE TABLE llx_projet_historial (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_projet integer NOT NULL,
  fk_task integer DEFAULT '0',
  email_send varchar(100) NOT NULL,
  emails varchar(255) NOT NULL,
  message text NOT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

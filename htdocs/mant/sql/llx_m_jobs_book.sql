CREATE TABLE llx_m_jobs_book (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_jobs integer NOT NULL,
  fk_book_det integer NOT NULL,
  sequen integer NOT NULL,
  answer text NOT NULL,
  date_answer date NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

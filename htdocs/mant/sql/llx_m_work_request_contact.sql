CREATE TABLE llx_m_work_request_contact (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_work_request integer NOT NULL,
  fk_contact integer NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL DEFAULT '0',
  datec date NOT NULL,
  datem date DEFAULT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_m_wcts (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  working_class varchar(30) NOT NULL,
  typemant varchar(30) NOT NULL,
  speciality varchar(30) NOT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

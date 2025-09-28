CREATE TABLE llx_m_location (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_property integer NOT NULL,
  detail varchar(255) NOT NULL
) ENGINE=InnoDB;

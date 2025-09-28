CREATE TABLE llx_p_blood_type (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  code varchar(30) NOT NULL,
  label varchar(50) NOT NULL,
  active smallint NOT NULL
) ENGINE=InnoDB;

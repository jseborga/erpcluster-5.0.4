CREATE TABLE llx_bank_url_advance (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_bank integer DEFAULT NULL,
  url_id integer DEFAULT NULL,
  url varchar(255) DEFAULT NULL,
  label varchar(255) DEFAULT NULL,
  type varchar(20) NOT NULL
) ENGINE=InnoDB;

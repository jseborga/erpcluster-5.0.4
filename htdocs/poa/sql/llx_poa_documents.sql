CREATE TABLE llx_poa_documents (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_type_con integer NOT NULL,
  code varchar(30) NOT NULL,
  detail varchar(200) NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  active tinyint DEFAULT '0' NULL
) ENGINE=InnoDB;

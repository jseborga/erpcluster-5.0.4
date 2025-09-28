CREATE TABLE llx_request_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_request_item integer NOT NULL,
  code varchar(50) COLLATE utf8_bin NOT NULL,
  name_doc varchar(50) COLLATE utf8_bin NOT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  status tinyint NOT NULL
) ENGINE=InnoDB;

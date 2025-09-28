CREATE TABLE llx_poa_activity_work (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_activity integer NOT NULL,
  fk_user integer NOT NULL,
  t1 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t2 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t3 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t4 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t5 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t6 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t7 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t8 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  t9 varchar(25) COLLATE utf8_bin DEFAULT NULL,
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

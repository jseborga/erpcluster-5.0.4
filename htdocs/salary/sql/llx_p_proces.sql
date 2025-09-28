CREATE TABLE llx_p_proces (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar( 3 ) NOT NULL,
  label varchar( 40 ) NOT NULL,
  state smallint NOT NULL
) ENGINE=innodb;

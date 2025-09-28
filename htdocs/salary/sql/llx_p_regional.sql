CREATE TABLE llx_p_regional (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar( 10 ) NOT NULL,
  label varchar( 40 ) NOT NULL,
  state tinyint NOT NULL
) ENGINE=innodb;

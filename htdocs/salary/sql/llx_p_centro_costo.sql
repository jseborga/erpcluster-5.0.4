CREATE TABLE llx_p_centro_costo
 (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer DEFAULT 1 NOT NULL,
  ref varchar( 9 ) NOT NULL,
  fk_cc_sup integer NOT NULL DEFAULT '0',
  label varchar( 40 ) NOT NULL,
  stipulation integer NOT NULL,
  locked integer NOT NULL,
  state integer NOT NULL
) ENGINE=innodb;

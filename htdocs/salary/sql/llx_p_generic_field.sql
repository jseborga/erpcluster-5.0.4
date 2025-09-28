CREATE TABLE llx_p_generic_field (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  generic_table_ref varchar(10) NOT NULL,
  sequen integer NOT NULL,
  field_value varchar( 50 ) NOT NULL
) ENGINE=innodb;

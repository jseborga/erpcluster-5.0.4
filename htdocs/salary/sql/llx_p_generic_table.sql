CREATE TABLE llx_p_generic_table (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(10) NOT NULL,
  table_cod varchar( 3 ) NOT NULL,
  table_name varchar( 40 ) NOT NULL,
  field_name varchar( 20 ) NOT NULL,
  sequen smallint NOT NULL,
  limits smallint NOT NULL DEFAULT '0',
  type_value smallint NOT NULL DEFAULT '0',
  state smallint NOT NULL
) ENGINE=InnoDB;



CREATE TABLE llx_stock_program (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  datep date NOT NULL,
  fk_entrepot integer NOT NULL,
  fk_type_movement integer NOT NULL,
  label text NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_user_val integer DEFAULT NULL,
  datec datetime NOT NULL,
  datem datetime NOT NULL,
  datev datetime DEFAULT NULL,
  tms timestamp NOT NULL,
  status_print tinyint NOT NULL DEFAULT '0',
  status tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;
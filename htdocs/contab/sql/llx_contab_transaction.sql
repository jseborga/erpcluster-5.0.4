CREATE TABLE llx_contab_transaction (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  type varchar(3) NOT NULL,
  bits varchar(6) NOT NULL,
  type_seat tinyint DEFAULT NULL,
  active tinyint DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

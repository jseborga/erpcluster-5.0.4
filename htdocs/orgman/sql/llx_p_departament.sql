CREATE TABLE llx_p_departament (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_father integer NOT NULL DEFAULT '0',
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  fk_user_resp integer DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  active tinyint NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_m_groups_program (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_group integer NOT NULL,
  fk_parent_previous integer NOT NULL DEFAULT '0',
  fk_type_repair integer NOT NULL,
  type varchar(1) NOT NULL,
  accountant integer NOT NULL,
  description text,
  fk_user_create integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL DEFAULT '0',
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
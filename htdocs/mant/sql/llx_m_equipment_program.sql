CREATE TABLE llx_m_equipment_program (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_equipment integer NOT NULL,
  fk_parent_previous integer DEFAULT '0' NOT NULL,
  fk_type_repair integer NOT NULL,
  accountant integer NOT NULL,
  description text,
  fk_user_create integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL DEFAULT '0',
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
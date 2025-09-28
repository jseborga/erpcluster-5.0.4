CREATE TABLE llx_budget_incidents_res (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_budget_incident integer NOT NULL,
  group_det tinyint NOT NULL DEFAULT '0',
  type varchar(30) NOT NULL,
  incident double(24,8) NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;

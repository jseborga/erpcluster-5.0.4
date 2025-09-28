CREATE TABLE llx_member_vacation (
  rowid integer AUTO_INCREMENT PRIMARY KEY NOT NULL,
  fk_member integer NOT NULL,
  date_ini date NOT NULL,
  date_fin date NOT NULL,
  period_year mediumint NOT NULL,
  days_assigned double NOT NULL,
  days_used double NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_user_app integer NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  datea date NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
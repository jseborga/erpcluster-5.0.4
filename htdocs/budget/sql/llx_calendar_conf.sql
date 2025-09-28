CREATE TABLE llx_calendar_conf (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_calendar integer NOT NULL,
  working_day varchar(15) NOT NULL,
  working_day_hours varchar(100) NOT NULL,
  nonwork_day varchar(15) NOT NULL,
  hours_day tinyint NOT NULL,
  hours_week tinyint NOT NULL,
  days_month tinyint NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_p_holiday (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer DEFAULT '1' NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(200) NOT NULL,
  date_day tinyint NOT NULL,
  date_month tinyint NOT NULL,
  date_year mediumint NOT NULL DEFAULT '0',
  type tinyint NOT NULL,
  fk_country integer NOT NULL,
  fk_region integer DEFAULT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
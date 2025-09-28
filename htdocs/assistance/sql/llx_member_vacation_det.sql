
CREATE TABLE llx_member_vacation_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_member_vacation integer NOT NULL,
  fk_licence integer NOT NULL,
  day_used double NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
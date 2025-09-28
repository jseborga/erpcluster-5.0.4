CREATE TABLE llx_m_property (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  address text NOT NULL,
  fk_country integer NULL,
  fk_state integer NULL,
  fk_user_create integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp,
  status tinyint DEFAULT '0'
) ENGINE=InnoDB;
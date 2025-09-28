CREATE TABLE llx_licences (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  fk_member integer NOT NULL,
  date_ini datetime NOT NULL,
  date_fin datetime NOT NULL,
  type_licence varchar(30) NOT NULL,
  detail text NOT NULL,
  date_create date NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  fk_user_aprob integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

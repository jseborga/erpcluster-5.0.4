CREATE TABLE llx_assistance_member (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_member integer NOT NULL,
  type_marking varchar(30) NOT NULL,
  aditional_time integer NOT NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NULL DEFAULT '0'
) ENGINE=InnoDB;

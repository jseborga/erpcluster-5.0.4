CREATE TABLE llx_assistance_def (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_reg integer NOT NULL,
  type_reg varchar(1) NOT NULL,
  type_marking varchar(30) NOT NULL,
  aditional_time integer NULL,
  fk_user_create integer NOT NULL,
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE llx_poa_structure_pl (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_structure integer NOT NULL,
  kind varchar(30) NOT NULL,
  gestion smallint NOT NULL,
  tmonth tinyint NOT NULL,
  quant double NOT NULL DEFAULT '0',
  fk_user_create integer NOT NULL,
  date_create datetime NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

CREATE TABLE llx_contab_periodo (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  period_month tinyint(4) NOT NULL,
  period_year smallint(6) NOT NULL,
  date_ini date NOT NULL,
  date_fin date NOT NULL,
  statut tinyint NOT NULL,
  status_af tinyint NOT NULL DEFAULT '0',
  status_al tinyint NOT NULL DEFAULT '0',
  status_co tinyint NOT NULL DEFAULT '0'

) ENGINE=InnoDB;
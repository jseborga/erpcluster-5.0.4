CREATE TABLE IF NOT EXISTS llx_contab_accountingspending (
  rowid int(11) NOT NULL AUTO_INCREMENT,
  ref varchar(20) NOT NULL,
  fk_contab_accounting int(11) NOT NULL,
  detail varchar(50) NOT NULL,
  status int(1) NOT NULL,
  PRIMARY KEY (rowid)
) ENGINE=InnoDB

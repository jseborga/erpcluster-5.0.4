CREATE TABLE llx_m_groups (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  fk_asset_group integer DEFAULT NULL,
  label varchar(255) NOT NULL,
  description text,
  useful_life double NOT NULL,
  percent double DEFAULT NULL,
  account_accounting varchar(30) DEFAULT NULL,
  account_spending varchar(30) DEFAULT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE llx_c_assets_group (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(255) NOT NULL,
  useful_life double NOT NULL,
  description text NULL,
  percent double NULL,
  fk_method_dep integer NULL,
  account_accounting varchar(30) NULL,
  account_spending varchar(30) NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;

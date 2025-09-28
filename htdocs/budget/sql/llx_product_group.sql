CREATE TABLE llx_product_group (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_father integer NOT NULL DEFAULT '0',
  fk_user_mod integer NOT NULL,
  fk_user_create integer NOT NULL,
  ref varchar(22) NOT NULL DEFAULT '',
  coderef varchar(10) NOT NULL DEFAULT '',
  sequence double(8,3) NOT NULL DEFAULT '',
  detail varchar(150) NOT NULL,
  detail_title varchar(100) NOT NULL,
  ref_name varchar(50) NOT NULL,
  percentage double(14,3) NOT NULL DEFAULT '0',
  operations varchar(100) NOT NULL,
  date_delete date NOT NULL DEFAULT 'NULL',
  date_create date NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE llx_contab_accounting (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  ref varchar(40) NOT NULL,
  entity integer NOT NULL,
  cta_class integer NOT NULL,
  cta_normal tinyint NOT NULL,
  cta_top integer NULL,
  cta_name varchar(255) NOT NULL,
  tms timestamp NOT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

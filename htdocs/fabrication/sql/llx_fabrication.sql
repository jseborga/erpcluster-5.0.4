CREATE TABLE IF NOT EXISTS llx_fabrication (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) NOT NULL,
  fk_commande integer NULL,
  date_creation date NOT NULL,
  date_delivery date NOT NULL,
  date_init datetime NULL,
  date_finish datetime NULL,
  description text NULL,
  model_pdf text NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

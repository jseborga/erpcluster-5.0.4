CREATE TABLE llx_poa_poai_rating (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_poai integer NOT NULL,
  nro_order tinyint NOT NULL,
  label varchar(255) NOT NULL,
  type_activity varchar(2) NOT NULL,
  relation_puesto varchar(100) NOT NULL,
  date_result date NOT NULL,
  means_verification varchar(255) NOT NULL,
  weighting double NOT NULL,
  type_weighting varchar(3) NOT NULL,
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

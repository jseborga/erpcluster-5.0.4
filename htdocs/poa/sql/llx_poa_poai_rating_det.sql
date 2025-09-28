CREATE TABLE llx_poa_poai_rating_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_poa_poai_rating integer NOT NULL,
  type_weighting integer NOT NULL,
  detail text NOT NULL,
  weighting double NOT NULL,
  date_create date NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;


CREATE TABLE llx_c_departements_region (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_region_geographic integer NOT NULL,
  fk_departement integer NOT NULL,
  active tinyint NOT NULL
) ENGINE=InnoDB;
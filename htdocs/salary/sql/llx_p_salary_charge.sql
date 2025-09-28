CREATE TABLE llx_p_salary_charge (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_charge integer NOT NULL,
  detail text NOT NULL,
  nivel smallint NOT NULL,
  salary_practiced double(15,2) NOT NULL,
  salary_market double(15,2) NOT NULL,
  salary_calc double(15,2) NOT NULL
) ENGINE=InnoDB;

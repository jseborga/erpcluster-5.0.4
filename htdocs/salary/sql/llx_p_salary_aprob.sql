CREATE TABLE llx_p_salary_aprob (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,	     
  type tinyint NOT NULL,
  fk_value integer NOT NULL,
  fk_aprobsup integer NULL,
  state tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

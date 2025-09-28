CREATE TABLE llx_c_element_task (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(50) NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;

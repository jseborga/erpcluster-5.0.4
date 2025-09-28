CREATE TABLE llx_c_pos_output_type (
  rowid integer PRIMARY KEY AUTO_INCREMENT,
  entity integer NOT NULL,
  code varchar(12) NOT NULL,
  label varchar(80) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
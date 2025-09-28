CREATE TABLE llx_c_resources_human (
  rowid integer NOT NULL,
  code varchar(10) DEFAULT NULL,
  label varchar(50) DEFAULT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
CREATE TABLE llx_cs_seatstype (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code varchar(30) NOT NULL,
  label varchar(50) NOT NULL,
  ref varchar(2) NOT NULL,
  active smallint NOT NULL
) ENGINE=InnoDB;

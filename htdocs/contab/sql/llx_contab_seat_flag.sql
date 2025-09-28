CREATE TABLE llx_contab_seat_flag (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_seat integer NOT NULL,
  table_nom varchar(254) NOT NULL,
  table_id integer NOT NULL,
  tms timestamp NOT NULL,
  state tinyint NOT NULL
) ENGINE=InnoDB;

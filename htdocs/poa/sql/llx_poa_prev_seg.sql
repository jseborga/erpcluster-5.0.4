CREATE TABLE llx_poa_prev_seg (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_prev integer NOT NULL,
  fk_father integer DEFAULT '0',
  fk_prev_ant integer DEFAULT '0',
  date_create date NOT NULL,
  fk_user_create integer NOT NULL,
  tms timestamp,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

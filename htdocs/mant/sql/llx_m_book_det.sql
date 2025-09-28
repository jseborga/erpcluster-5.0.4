CREATE TABLE llx_m_book_det (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  code_insp_book varchar(30) NOT NULL,
  ref varchar(30) NOT NULL,
  detail text,
  type_campo varchar(30) NOT NULL,
  tms timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

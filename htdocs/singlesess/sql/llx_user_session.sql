CREATE TABLE llx_user_session	(
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_user integer NOT NULL,
  nro_ip varchar(30) NOT NULL,
  sessionid varchar(150) NOT NULL,
  csession varchar(100) NOT NULL,
  ccode varchar(20) NULL,
  datec datetime NOT NULL,
  dateu datetime NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;	
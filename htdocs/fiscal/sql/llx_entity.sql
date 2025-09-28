-- ===========================================================================
-- ===========================================================================

create table llx_entity
(
  rowid				integer AUTO_INCREMENT PRIMARY KEY,
  tms				timestamp,
  label				varchar(255) NOT NULL,
  description		text,
  datec				datetime,
  fk_user_creat		integer,
  options			text,
  visible			tinyint DEFAULT 1 NOT NULL,
  active			tinyint DEFAULT 1 NOT NULL
  
) ENGINE=innodb;
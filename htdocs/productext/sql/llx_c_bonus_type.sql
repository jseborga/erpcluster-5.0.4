CREATE TABLE llx_c_bonus_type (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  ref varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  label varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  active tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
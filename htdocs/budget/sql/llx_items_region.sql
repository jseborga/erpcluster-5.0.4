CREATE TABLE llx_items_region (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_item integer NOT NULL,
  fk_region integer NOT NULL,
  fk_sector integer NOT NULL DEFAULT '0',
  hour_production double(24,8) NOT NULL,
  amount_noprod double(24,8) NOT NULL,
  amount double(24,8) NOT NULL,
  active tinyint DEFAULT '1' NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
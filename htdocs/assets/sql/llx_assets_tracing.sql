CREATE TABLE llx_assets_tracing (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_asset integer NOT NULL,
  fk_user_resp integer NOT NULL,
  been varchar(30) NOT NULL,
  description text NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  dater date NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL DEFAULT '1'
) ENGINE=InnoDB;
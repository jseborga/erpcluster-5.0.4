CREATE TABLE llx_assets_doc (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  fk_asset integer NOT NULL,
  fk_cassetdoc integer NOT NULL,
  dater date NOT NULL,
  label text NOT NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec date NOT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint NOT NULL
) ENGINE=InnoDB;
CREATE TABLE IF NOT EXISTS llx_entrepot_relation (
  rowid integer PRIMARY KEY NOT NULL,
  fk_entrepot_father integer NOT NULL,
  fk_projet integer NULL,
  tipo varchar(30) NOT NULL,
  model_pdf text NULL
) ENGINE=InnoDB;

ALTER TABLE llx_projet_add ADD UNIQUE KEY uk_unique (fk_projet);
ALTER TABLE llx_projet_add ADD origin varchar(50) NULL after use_resource;
ALTER TABLE llx_projet_add ADD originid integer DEFAULT '0' NULL after origin;
ALTER TABLE llx_projet_add ADD fk_entrepot INTEGER NULL DEFAULT '0' AFTER fk_projet;


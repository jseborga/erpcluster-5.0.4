ALTER TABLE llx_c_type_tva ADD fk_pays INTEGER DEFAULT '0' NOT NULL after rowid;

ALTER TABLE llx_c_type_tva ADD UNIQUE uk_unique (fk_pays, code);


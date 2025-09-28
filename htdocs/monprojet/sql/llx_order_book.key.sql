ALTER TABLE llx_order_book ADD UNIQUE uk_unique (fk_projet, fk_contrat, ref);
ALTER TABLE llx_order_book ADD fk_parent INTEGER NOT NULL DEFAULT '0' AFTER rowid;
ALTER TABLE llx_order_book ADD document TEXT NULL AFTER detail;
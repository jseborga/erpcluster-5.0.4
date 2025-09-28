ALTER TABLE llx_poa_contrat_appoint ADD UNIQUE UK_UNIQUE (fk_contrat, fk_user, date_appoint);
ALTER TABLE llx_poa_contrat_appoint CHANGE date_appoint date_appoint DATE NOT NULL;

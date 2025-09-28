ALTER TABLE llx_cs_indexes_country ADD UNIQUE uk_unique ( entity , ref , date_ind );

ALTER TABLE llx_cs_indexes_country DROP FOREIGN KEY llx_cs_indexes_country_ibfk_1;
ALTER TABLE llx_cs_indexes_country ADD CONSTRAINT idk_csindexescountry_entity_ref FOREIGN KEY (entity, ref) REFERENCES llx_cs_currency_type(entity, ref) ON DELETE RESTRICT ON UPDATE CASCADE;
ALTER TABLE llx_city ADD UNIQUE uk_unique ( fk_country , fk_departament , ref );
ALTER TABLE llx_city CHANGE fk_departament fk_departement INTEGER NOT NULL;
ALTER TABLE llx_city DROP INDEX uk_unique;
ALTER TABLE llx_city ADD UNIQUE uk_unique (fk_country, fk_departement, ref);
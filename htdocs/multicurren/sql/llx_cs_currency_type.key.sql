ALTER TABLE llx_cs_currency_type ADD UNIQUE INDEX uk_cs_currencytype_entity_ref(entity,ref);
ALTER TABLE llx_cs_currency_type ADD order_currency TINYINT NOT NULL AFTER registry;
ALTER TABLE llx_cs_currency_type ADD fk_user_create integer NOT NULL AFTER order_currency;
ALTER TABLE llx_cs_currency_type ADD fk_user_mod integer NOT NULL AFTER fk_user_create;
ALTER TABLE llx_cs_currency_type ADD datec date NULL AFTER fk_user_create;
ALTER TABLE llx_cs_currency_type ADD dateu date NULL AFTER datec;
ALTER TABLE llx_cs_currency_type ADD tms timestamp NOT NULL AFTER dateu;
ALTER TABLE llx_cs_currency_type CHANGE state status TINYINT NOT NULL;

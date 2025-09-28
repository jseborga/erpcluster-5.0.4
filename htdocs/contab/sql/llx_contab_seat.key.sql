ALTER TABLE llx_contab_seat ADD type_numeric VARCHAR( 2 ) NOT NULL AFTER type_seat;
ALTER TABLE llx_contab_seat ADD sequential VARCHAR(10) NOT NULL AFTER type_seat ;
ALTER TABLE llx_contab_seat ADD seat_month VARCHAR( 2 ) NOT NULL AFTER sequential;
ALTER TABLE llx_contab_seat ADD seat_year YEAR NOT NULL AFTER seat_month ;

ALTER TABLE llx_contab_seat DROP INDEX uk_entity_lote_sblote_doc;
ALTER TABLE llx_contab_seat ADD UNIQUE INDEX uk_entity_lote_sblote_doc ( entity,seat_year,lote,sblote,doc );

ALTER TABLE llx_contab_seat DROP INDEX uk_entity_typenumeric_seatmonth_sequential;

ALTER TABLE llx_contab_seat ADD fk_user_create INTEGER NULL AFTER manual;
ALTER TABLE llx_contab_seat ADD fk_user_mod INTEGER NULL AFTER fk_user_create;
ALTER TABLE llx_contab_seat ADD datec DATE NULL AFTER fk_user_mod;
ALTER TABLE llx_contab_seat ADD datem DATE NULL AFTER datec;
ALTER TABLE llx_contab_seat ADD tms TIMESTAMP NOT NULL AFTER datem;
ALTER TABLE llx_contab_seat ADD cbttipo VARCHAR(2) NULL AFTER cbter;

ALTER TABLE llx_contab_seat CHANGE state status TINYINT NOT NULL;
ALTER TABLE llx_contab_seat CHANGE sblote sblote VARCHAR(6) NOT NULL;
ALTER TABLE llx_contab_seat CHANGE currency currency VARCHAR(3) NOT NULL;
ALTER TABLE llx_contab_seat CHANGE seat_month seat_month TINYINT NOT NULL;
ALTER TABLE llx_contab_seat CHANGE seat_year seat_year SMALLINT NOT NULL;
ALTER TABLE llx_contab_seat DROP INDEX uk_entity_typenumeric_seatmonth_sequential;

ALTER TABLE llx_contab_seat CHANGE history history TEXT NULL DEFAULT NULL;
ALTER TABLE llx_contab_seat CHANGE fk_user_creator fk_user_create INTEGER NOT NULL;

ALTER TABLE llx_contab_seat DROP INDEX uk_unique_ref;
ALTER TABLE llx_contab_seat ADD UNIQUE uk_unique_ref(entity, seat_year,ref);


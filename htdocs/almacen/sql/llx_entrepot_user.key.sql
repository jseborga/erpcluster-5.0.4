ALTER TABLE llx_entrepot_user ADD uk_unique UNIQUE (fk_entrepot,fk_user);

ALTER TABLE llx_entrepot_user ADD type TINYINT NOT NULL DEFAULT '1' AFTER fk_user;
ALTER TABLE llx_entrepot_user ADD typeapp TINYINT NOT NULL DEFAULT '0' AFTER type;
ALTER TABLE llx_bank_historial DROP INDEX uk_unique;
ALTER TABLE llx_bank_historial ADD label VARCHAR(200) NOT NULL AFTER fk_user_to;
ALTER TABLE llx_bank_historial CHANGE date_transfer date_transfer DATETIME NOT NULL;
ALTER TABLE llx_account_user ADD UNIQUE uk_unique (fk_account, fk_user);
ALTER TABLE llx_account_user CHANGE statut status TINYINT NOT NULL ;

ALTER TABLE llx_localtax ADD fk_typepayment INTEGER NULL AFTER fk_user_modif;
ALTER TABLE llx_localtax ADD num_payment VARCHAR(50) NULL AFTER fk_typepayment;
ALTER TABLE llx_localtax ADD import_key VARCHAR(14) NULL AFTER num_payment;
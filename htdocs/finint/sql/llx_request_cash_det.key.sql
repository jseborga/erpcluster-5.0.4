ALTER TABLE llx_request_cash_det ADD UNIQUE KEY uk_unique (fk_request, detail);
ALTER TABLE llx_request_cash_det ADD fk_unit INTEGER NOT NULL DEFAULT '0' AFTER fk_request;
ALTER TABLE llx_request_cash_det ADD quant DOUBLE(18,5) NULL DEFAULT '0' AFTER fk_unit;
ALTER TABLE llx_request_cash_det CHANGE statut status TINYINT NOT NULL ;
ALTER TABLE llx_request_cash_log ADD amount DOUBLE NULL DEFAULT '0' AFTER description;
ALTER TABLE llx_request_cash_log ADD status_cash TINYINT NOT NULL DEFAULT '0' AFTER amount;
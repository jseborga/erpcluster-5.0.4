ALTER TABLE llx_incidents ADD code_parameter varchar(30) NOT NULL AFTER label;

ALTER TABLE llx_incidents ADD cost_direct DOUBLE(24,8) NOT NULL DEFAULT '0' AFTER njobs;
ALTER TABLE llx_incidents ADD time_duration INTEGER NOT NULL DEFAULT '0' AFTER cost_direct;
ALTER TABLE llx_incidents ADD exchange_rate DOUBLE(10,3) NOT NULL DEFAULT '0' AFTER time_duration;
ALTER TABLE llx_incidents ADD commission DOUBLE(24,8) NULL DEFAULT '0' AFTER ponderation;
ALTER TABLE llx_incidents ADD day_efective_month INTEGER NULL DEFAULT '0' AFTER day_efective;
ALTER TABLE llx_incidents ADD tva_tx DOUBLE(24,8) NULL DEFAULT '0' AFTER exchange_rate;
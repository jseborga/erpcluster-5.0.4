ALTER TABLE llx_parameter_calculation ADD UNIQUE uk_unique (entity,code);

ALTER TABLE llx_parameter_calculation ADD type VARCHAR(30) NULL DEFAULT NULL AFTER label;
ALTER TABLE llx_parameter_calculation ADD amount DOUBLE(24,5) NOT NULL DEFAULT '0' AFTER type;

ALTER TABLE llx_contab_periodo ADD UNIQUE uk_unique (entity, period_month, period_year);

ALTER TABLE llx_contab_periodo ADD status_af TINYINT NOT NULL DEFAULT '0' AFTER statut;
ALTER TABLE llx_contab_periodo ADD status_al TINYINT NOT NULL DEFAULT '0' AFTER status_af;

ALTER TABLE llx_contab_periodo CHANGE period_year period_year SMALLINT NOT NULL;
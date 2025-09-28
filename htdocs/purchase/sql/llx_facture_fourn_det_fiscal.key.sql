ALTER TABLE llx_facture_fourn_det_fiscal ADD UNIQUE uk_unique (fk_facture_fourn_det, code_tva);

ALTER TABLE llx_facture_fourn_det_fiscal ADD amount_base DOUBLE NOT NULL AFTER total_ttc;
ALTER TABLE llx_facture_fourn_det_fiscal ADD amount_ice DOUBLE NULL DEFAULT '0' AFTER amount_base;
ALTER TABLE llx_facture_fourn_det_fiscal ADD discount DOUBLE NULL DEFAULT '0' AFTER amount_ice;
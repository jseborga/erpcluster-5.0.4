ALTER TABLE llx_facture_fourn_det_add ADD UNIQUE uk_unique (fk_facture_fourn_det);

ALTER TABLE llx_facture_fourn_det_add ADD fk_object INTEGER NULL AFTER fk_facture_fourn_det;
ALTER TABLE llx_facture_fourn_det_add ADD object VARCHAR(255) NULL AFTER fk_object;
ALTER TABLE llx_facture_fourn_det_add ADD fk_asset INTEGER NULL AFTER discount;
ALTER TABLE llx_facture_fourn_det_add ADD fk_fabrication INTEGER NULL DEFAULT '0' AFTER object;
ALTER TABLE llx_facture_fourn_det_add ADD fk_fabricationdet INTEGER NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_facture_fourn_det_add ADD fk_projet INTEGER NULL DEFAULT '0' AFTER fk_fabricationdet;
ALTER TABLE llx_facture_fourn_det_add ADD fk_projet_task INTEGER NULL DEFAULT '0' AFTER fk_projet;
ALTER TABLE llx_facture_fourn_det_add ADD fk_jobs INTEGER NULL DEFAULT '0' AFTER fk_projet_task;
ALTER TABLE llx_facture_fourn_det_add ADD fk_jobsdet INTEGER NULL DEFAULT '0' AFTER fk_jobs;
ALTER TABLE llx_facture_fourn_det_add ADD fk_structure INTEGER NULL DEFAULT '0' AFTER fk_jobsdet;
ALTER TABLE llx_facture_fourn_det_add ADD fk_poa INTEGER NULL DEFAULT '0' AFTER fk_structure;
ALTER TABLE llx_facture_fourn_det_add ADD partida VARCHAR(10) DEFAULT NULL AFTER fk_poa;
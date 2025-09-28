ALTER TABLE llx_supplier_proposaldet_add ADD UNIQUE uk_unique (fk_supplier_proposaldet);


ALTER TABLE llx_supplier_proposaldet_add ADD fk_fabrication INTEGER NULL DEFAULT '0' AFTER object;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_fabricationdet INTEGER NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_projet INTEGER NULL DEFAULT '0' AFTER fk_fabricationdet;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_projet_task INTEGER NULL DEFAULT '0' AFTER fk_projet;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_jobs INTEGER NULL DEFAULT '0' AFTER fk_projet_task;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_jobsdet INTEGER NULL DEFAULT '0' AFTER fk_jobs;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_structure INTEGER NULL DEFAULT '0' AFTER fk_jobsdet;
ALTER TABLE llx_supplier_proposaldet_add ADD fk_poa INTEGER NULL DEFAULT '0' AFTER fk_structure;
ALTER TABLE llx_supplier_proposaldet_add ADD partida VARCHAR(10) DEFAULT NULL AFTER fk_poa;
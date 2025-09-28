ALTER TABLE llx_purchase_requestdet ADD INDEX idx_fk_purchase_request ( fk_purchase_request );


ALTER TABLE llx_purchase_requestdet ADD origin VARCHAR(150) NULL AFTER ref_fourn;
ALTER TABLE llx_purchase_requestdet ADD originid INTEGER NULL AFTER origin;

ALTER TABLE llx_purchase_requestdet ADD tva_tx DOUBLE DEFAULT '0' NULL AFTER fk_unit;
ALTER TABLE llx_purchase_requestdet ADD subprice DOUBLE DEFAULT '0' NULL AFTER tva_tx;
ALTER TABLE llx_purchase_requestdet ADD price DOUBLE DEFAULT '0' NULL AFTER subprice;
ALTER TABLE llx_purchase_requestdet ADD total_ht DOUBLE DEFAULT '0' NULL AFTER price;
ALTER TABLE llx_purchase_requestdet ADD total_ttc DOUBLE DEFAULT '0' NULL AFTER total_ht;

ALTER TABLE llx_purchase_requestdet ADD fk_user_create INTEGER NOT NULL AFTER originid;
ALTER TABLE llx_purchase_requestdet ADD fk_user_mod INTEGER NOT NULL AFTER fk_user_create;
ALTER TABLE llx_purchase_requestdet ADD datec DATE NOT NULL AFTER fk_user_mod;
ALTER TABLE llx_purchase_requestdet ADD datem DATE NOT NULL AFTER datec;
ALTER TABLE llx_purchase_requestdet ADD tms TIMESTAMP NOT NULL AFTER datem;

ALTER TABLE llx_purchase_requestdet ADD fk_fabrication INTEGER NULL DEFAULT '0' AFTER fk_product;
ALTER TABLE llx_purchase_requestdet ADD fk_fabricationdet INTEGER NULL DEFAULT '0' AFTER fk_fabrication;
ALTER TABLE llx_purchase_requestdet ADD fk_projet INTEGER NULL DEFAULT '0' AFTER fk_fabricationdet;
ALTER TABLE llx_purchase_requestdet ADD fk_projet_task INTEGER NULL DEFAULT '0' AFTER fk_projet;
ALTER TABLE llx_purchase_requestdet ADD fk_jobs INTEGER NULL DEFAULT '0' AFTER fk_projet_task;
ALTER TABLE llx_purchase_requestdet ADD fk_jobsdet INTEGER NULL DEFAULT '0' AFTER fk_jobs;
ALTER TABLE llx_purchase_requestdet ADD fk_structure INTEGER NULL DEFAULT '0' AFTER fk_jobsdet;
ALTER TABLE llx_purchase_requestdet ADD fk_poa INTEGER NULL DEFAULT '0' AFTER fk_structure;
ALTER TABLE llx_purchase_requestdet ADD partida VARCHAR(10) NULL AFTER fk_poa;
ALTER TABLE llx_purchase_requestdet ADD fk_poa_partida_pre_det INTEGER NULL DEFAULT '0' AFTER fk_poa;
ALTER TABLE llx_purchase_requestdet ADD fk_commande_fournisseurdet INTEGER NULL DEFAULT '0' AFTER fk_poa_partida_pre_det;
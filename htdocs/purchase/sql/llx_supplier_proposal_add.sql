CREATE TABLE llx_supplier_proposal_add (
  rowid integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  fk_supplier_proposal integer NOT NULL,
  fk_purchase_request integer DEFAULT NULL,
  fk_pays integer DEFAULT NULL,
  fk_province integer DEFAULT NULL,
  code_facture varchar(12) NULL,
  code_type_purchase varchar(12) NULL,
  fk_user_create integer NOT NULL,
  fk_user_mod integer NOT NULL,
  datec datetime DEFAULT NULL,
  datem date NOT NULL,
  tms timestamp NOT NULL,
  status tinyint DEFAULT '0' NOT NULL
) ENGINE=InnoDB;
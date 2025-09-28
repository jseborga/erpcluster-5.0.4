CREATE TABLE llx_items_det (
  rowid integer NOT NULL,
  fk_item integer NOT NULL,
  fk_pu_structure integer NOT NULL,
  fk_object integer DEFAULT NULL,
  type varchar(24) DEFAULT NULL,
  detail varchar(150) DEFAULT NULL,
  fk_unit integer NOT NULL,
  quant double(24,5) NOT NULL,
  price double(24,5) NOT NULL,
  active tinyint NOT NULL,
  statut tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

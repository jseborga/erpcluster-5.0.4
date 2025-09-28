CREATE TABLE llx_poa_pac (
  rowid integer AUTO_INCREMENT PRIMARY KEY,
  entity integer NOT NULL,
  fk_poa integer DEFAULT NULL,
  gestion smallint NOT NULL,
  fk_type_modality integer NOT NULL,
  fk_type_object integer NOT NULL,
  ref integer NOT NULL,
  nom varchar(255) NOT NULL,
  fk_financer integer NOT NULL,
  month_init tinyint NOT NULL,
  month_public tinyint NOT NULL,
  partida varchar(30) DEFAULT NULL,
  amount double DEFAULT '0',
  fk_user_resp integer NULL,
  responsible varchar(150) DEFAULT NULL,
  tms datetime NOT NULL,
  statut tinyint NOT NULL
) ENGINE=InnoDB;

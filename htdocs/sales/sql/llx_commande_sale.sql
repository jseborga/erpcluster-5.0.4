CREATE TABLE llx_commande_sale (
rowid INTEGER PRIMARY KEY AUTO_INCREMENT ,
fk_commande INTEGER NOT NULL ,
fk_subsidiary INTEGER NOT NULL ,
fk_entrepot INTEGER NOT NULL ,
fk_entrepot_end INTEGER NULL,
date_livraison DATETIME NULL,
amount_advance DOUBLE(20,5) DEFAULT '0',
tms TIMESTAMP NOT NULL ,
statut TINYINT NOT NULL
)
ENGINE = InnoDB;
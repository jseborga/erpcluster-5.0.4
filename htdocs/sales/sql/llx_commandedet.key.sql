ALTER TABLE llx_commandedet ADD origin varchar(50) NULL after fk_unit;
ALTER TABLE llx_commandedet ADD origin_id integer DEFAULT '0' NULL after origin;
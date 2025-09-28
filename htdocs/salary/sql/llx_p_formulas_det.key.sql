ALTER TABLE llx_p_formulas_det ADD UNIQUE INDEX uk_pformulasdet_entity_refformula_sequen(entity,ref_formula,sequen);


ALTER TABLE llx_p_formulas_det ADD nmonth TINYINT NULL DEFAULT NULL AFTER changefull;
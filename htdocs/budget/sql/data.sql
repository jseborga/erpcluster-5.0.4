ALTER TABLE llx_c_type_contact CHANGE rowid rowid INTEGER NOT NULL AUTO_INCREMENT;

INSERT INTO llx_c_type_contact (element, source, code, libelle, active, module, position) VALUES
('budget', 'internal', 'BUDGETCONTRIBUTOR', 'Participante', 1, NULL, 0),
('budget', 'internal', 'BUDGETLEADER', 'Jefe de Proyecto', 1, NULL, 0),
('budget', 'external', 'BUDGETLEADER', 'Jefe de Proyecto', 1, NULL, 0),
('budget', 'external', 'BUDGETCONTRIBUTOR', 'Participante', 1, NULL, 0);

INSERT INTO llx_c_type_contact (rowid, element, source, code, libelle, active, module, position) VALUES
(201,'budgettask', 'internal', 'TASKEXECUTIVE', 'Responsable', 1, NULL, 0),
(202,'budgettask', 'internal', 'TASKCONTRIBUTOR', 'Participante', 1, NULL, 0),
(203,'budgettask', 'external', 'TASKEXECUTIVE', 'Responsable', 1, NULL, 0),
(204,'budgettask', 'external', 'TASKCONTRIBUTOR', 'Participante', 1, NULL, 0);

INSERT INTO llx_c_type_contact (rowid, element, source, code, libelle, active, module, position) VALUES
(205,'budget', 'internal', 'BUDGETREVISOR', 'Revisor', 1, NULL, 0);


INSERT INTO llx_pu_operator (rowid, detail, operator, type, statut) VALUES
(1, 'Sumar', '+', 1, 1),
(2, 'Restar', '-', 1, 1),
(3, 'Multiplicar', '*', 1, 1),
(4, 'Dividir', '/', 1, 1),
(5, 'Sumar Columna', 'sum()', 2, 1);

INSERT INTO llx_pu_type_structure (entity, code, label, fk_user_create, fk_user_mod, date_create, date_mod, tms, active) VALUES
('__ENTITY__', 'PU001', 'Estructura para GAMLP', 39, 39, '2016-10-25', '2016-10-25', '2016-10-25 22:48:56', 1);

INSERT INTO llx_pu_structure (entity, ref, group_structure, fk_user_create, fk_user_mod, fk_categorie, type_structure, detail, ordby, date_delete, date_create, date_mod, tms, status) VALUES
('__ENTITY__', 'MAT', 'MA',1, 1, 60, 'PU001', 'MATERIALES', 1, NULL, '2008-08-31', '2018-01-01', '2016-10-25 22:52:46', 1),
('__ENTITY__', 'MDO', 'MO',1, 1, 61, 'PU001', 'MANO DE OBRA', 5, NULL, '2007-06-22', '2018-01-01', '2016-10-25 22:52:51', 1),
('__ENTITY__', 'EMH', 'MQ',1, 1, 62, 'PU001', 'EQUIPO, MAQUINARIA, HERRAMIENTAS', 10, NULL, '2007-06-22', '2018-01-01', '2016-10-25 22:52:55', 1),
('__ENTITY__', 'GGA', 'OT',1, 1, NULL, 'PU001', 'GASTOS GENERALES ADMINISTRATIVOS', 15, NULL, '2007-06-22', '2018-01-01', '2016-10-25 22:53:01', 1),
('__ENTITY__', 'UTI', 'OT',1, 1, NULL, 'PU001', 'UTILIDAD', 20, NULL, '2007-06-22', '2018-01-01', '2016-10-25 22:53:04', 1),
('__ENTITY__', 'IMP', 'OT',1, 1, NULL, 'PU001', 'IMPUESTOS', 25, NULL, '2007-06-22', '2018-01-01', '2016-10-25 22:53:09', 1);



INSERT INTO llx_pu_structure_det (entity, ref_structure, type_structure, sequen, detail, formula, status_print, fk_user_create, fk_user_mod, date_create, date_mod, tms, status) VALUES
('__ENTITY__', 'MAT', 'PU001', 5, 'Suma materiales', '001', '0', 39, 39, '2016-10-24', '2016-10-24', '2016-11-14 14:09:09', 1),
('__ENTITY__', 'MDO', 'PU001', 5, 'Suma Mano de obra', '010', '0', 39, 39, '2016-10-24', '2016-10-24', '2016-11-14 14:09:13', 1),
('__ENTITY__', 'MDO', 'PU001', 10, 'Beneficios Sociales', '020', '1', 39, 39, '2016-10-24', '2016-10-24', '2016-11-14 14:09:17', 1),
('__ENTITY__', 'MDO', 'PU001', 15, 'IVA', '025', '1', 39, 39, '2016-10-24', '2016-10-24', '2016-11-14 14:09:23', 1),
('__ENTITY__', 'GGA', 'PU001', 5, 'Gastos Generales', '040', '1', 39, 39, '2016-10-24', '2016-10-24', '2016-11-14 14:08:16', 1),
('__ENTITY__', 'EMH', 'PU001', 5, 'Suma Equipo y Maquinaria', '015', '1', 39, 39, '2016-10-25', '2016-10-25', '2016-11-14 14:06:42', 1),
('__ENTITY__', 'EMH', 'PU001', 10, 'Herramientas - % de la Mano de Obra', '030', '1', 39, 39, '2016-10-25', '2016-10-25', '2016-11-14 14:06:46', 1),
('__ENTITY__', 'UTI', 'PU001', 5, 'Utilidad - % de 1+2+3+4', '050', '1', 39, 39, '2016-10-25', '2016-10-25', '2016-11-14 14:08:41', 1),
('__ENTITY__', 'IMP', 'PU001', 5, 'Impuesto a las Transacciones - % de 1+2+3+4+5', '060', '1', 39, 39, '2016-10-25', '2016-10-25', '2016-11-14 14:08:57', 1);


INSERT INTO llx_pu_formulas (entity, ref, detail, statut) VALUES
('__ENTITY__', '001', 'Suma materiales', 1),
('__ENTITY__', '010', 'Suma mano de obra', 1),
('__ENTITY__', '015', 'Suma Equipo y Maquinaria', 1),
('__ENTITY__', '020', 'Beneficios Sociales', 1),
('__ENTITY__', '025', 'Impuestos al valor agregado', 1),
('__ENTITY__', '030', 'Herramientas - % de la mano de obra', 1),
('__ENTITY__', '040', 'Gastos Generales', 1),
('__ENTITY__', '050', 'Utilidad', 1),
('__ENTITY__', '060', 'Impuestos', 0);

INSERT INTO llx_pu_formulas_det (entity, ref_formula, fk_operator, type, changefull, sequen, status) VALUES
('__ENTITY__', '001', 5, 'pu_structure', '1|MAT', 1, 1),
('__ENTITY__', '010', 5, 'pu_structure', '1|MDO', 1, 1),
('__ENTITY__', '015', 5, 'pu_structure', '1|EMH', 1, 1),
('__ENTITY__', '020', 1, 'pu_formulas', '1|010', 1, 1),
('__ENTITY__', '020', 3, 'valor', '71.18', 2, 1),
('__ENTITY__', '020', 4, 'valor', '100', 3, 1),
('__ENTITY__', '025', 1, 'pu_formulas', '1|010', 1, 1),
('__ENTITY__', '025', 1, 'pu_formulas', '1|020', 2, 1),
('__ENTITY__', '025', 3, 'valor', '14.94', 3, 1),
('__ENTITY__', '025', 4, 'valor', '100', 4, 1),
('__ENTITY__', '028', 1, 'pu_formulas', '1|010', 1, -1),
('__ENTITY__', '028', 1, 'pu_formulas', '1|020', 2, -1),
('__ENTITY__', '028', 1, 'pu_formulas', '1|025', 3, -1),
('__ENTITY__', '030', 1, 'pu_formulas', '1|028', 1, -1),
('__ENTITY__', '030', 3, 'valor', '5', 2, -1),
('__ENTITY__', '030', 4, 'valor', '100', 3, -1),
('__ENTITY__', '012', 1, 'pu_formulas', '1|010', 1, -1),
('__ENTITY__', '012', 1, 'pu_formulas', '1|020', 2, -1),
('__ENTITY__', '012', 1, 'pu_formulas', '1|025', 3, -1),
('__ENTITY__', '035', 1, 'pu_formulas', '1|015', 1, -1),
('__ENTITY__', '035', 1, 'pu_formulas', '1|030', 2, -1),
('__ENTITY__', '038', 1, 'pu_formulas', '1|001', 1, -1),
('__ENTITY__', '038', 1, 'pu_formulas', '1|028', 2, -1),
('__ENTITY__', '038', 1, 'pu_formulas', '1|035', 3, -1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|038', 1, -1),
('__ENTITY__', '040', 3, 'valor', '11', 2, -1),
('__ENTITY__', '040', 4, 'valor', '100', 3, -1),
('__ENTITY__', '030', 1, 'pu_formulas', '1|010', 4, 1),
('__ENTITY__', '030', 1, 'pu_formulas', '1|020', 5, 1),
('__ENTITY__', '030', 1, 'pu_formulas', '1|025', 6, 1),
('__ENTITY__', '030', 3, 'valor', '5', 7, 1),
('__ENTITY__', '030', 4, 'valor', '100', 8, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|001', 4, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|010', 5, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|020', 6, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|025', 7, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|015', 8, 1),
('__ENTITY__', '040', 1, 'pu_formulas', '1|030', 9, 1),
('__ENTITY__', '040', 3, 'valor', '11', 10, 1),
('__ENTITY__', '040', 4, 'valor', '100', 11, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|001', 1, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|010', 2, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|015', 3, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|020', 4, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|025', 5, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|030', 6, 1),
('__ENTITY__', '050', 1, 'pu_formulas', '1|040', 7, 1),
('__ENTITY__', '050', 3, 'valor', '7', 8, 1),
('__ENTITY__', '050', 4, 'valor', '100', 9, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|001', 1, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|010', 2, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|015', 3, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|020', 4, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|025', 5, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|030', 6, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|040', 7, 1),
('__ENTITY__', '060', 1, 'pu_formulas', '1|050', 8, 1),
('__ENTITY__', '060', 3, 'valor', '3.09', 9, 1),
('__ENTITY__', '060', 4, 'valor', '100', 10, 1);

INSERT INTO llx_parameter_calculation (entity, code, label, datec, datem, fk_user_create, fk_user_mod, tms, active, status) VALUES
('__ENTITY__', 'BENESOC', 'Beneficios Sociales', '2017-01-01', '2018-04-13', 1, 5, '2018-04-13 22:35:07', 1, 1),
('__ENTITY__', 'UTILITY', 'Utilidades', '2017-01-01', '2017-01-01', 1, 1, '2017-01-17 06:45:01', 1, 1),
('__ENTITY__', 'TAXES_IVA', 'Impuestos IVA', '2017-01-01', '2017-01-01', 1, 1, '2017-01-17 06:45:01', 1, 1),
('__ENTITY__', 'TAXES', 'Impuestos IT', '2017-01-01', '2017-02-13', 1, 2, '2017-02-14 03:09:35', 1, 1),
('__ENTITY__', 'GGAA', 'Gastos Generales', '2017-02-01', '2017-02-01', 19, 19, '2017-02-01 23:10:48', 1, 1),
('__ENTITY__', 'IVA_MdO', 'IVA Mano de Obra efectiva 14,94', '2017-02-01', '2017-02-07', 19, 19, '2017-02-07 05:26:08', 1, 1),
('__ENTITY__', 'iva_NOMINAL', 'IVA_NOMINAL_13%', '2017-02-07', '2017-02-07', 2, 2, '2017-02-07 04:56:24', 1, 1),
('__ENTITY__', 'Herra_meno', 'Herramientas Menores 5%', '2017-02-07', '2017-02-07', 19, 19, '2017-02-07 05:54:50', 1, 1),
('__ENTITY__', 'IMPREVISTOS', 'Gastos imprevistos', '2017-02-13', '2017-02-13', 2, 2, '2017-02-14 03:00:11', 1, 1),
('__ENTITY__', 'PERMOIND', 'Mano de Obra Indirecta', '2018-04-16', '2018-04-16', 6, 6, '2018-04-16 12:21:55', 0, 1);

INSERT INTO `llx_pu_variables` (`entity`, `ref`, `label`, `fk_unit`, `fk_user_create`, `fk_user_mod`, `datec`, `datem`, `tms`, `status`) VALUES
('__ENTITY__', 'AA', 'Capacidad', 6, 6, 6, '2018-04-16', '2018-04-17', '2018-04-17 17:32:38', 1),
('__ENTITY__', 'AB', 'Consumo', 12, 6, 6, '2018-04-17', '2018-04-17', '2018-04-17 17:35:05', 1),
('__ENTITY__', 'AC', 'Espaciamiento', 8, 6, 6, '2018-04-17', '2018-04-17', '2018-04-17 17:35:10', 1),
('__ENTITY__', 'AD', 'Ancho de operación', 8, 6, 6, '2018-04-17', '2018-04-17', '2018-04-17 17:35:16', 1),
('__ENTITY__', 'AE', 'Ancho de traslape', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:24:26', 0),
('__ENTITY__', 'AF', 'Profundiad de Trabajo', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:24:50', 0),
('__ENTITY__', 'AG', 'Número de pasadas', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:25:33', 1),
('__ENTITY__', 'AH', 'Ancho util de operación', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:25:49', 1),
('__ENTITY__', 'AI', 'Alejamiento', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:26:01', 1),
('__ENTITY__', 'AJ', 'Espesor', 11, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:26:17', 1),
('__ENTITY__', 'FA', 'Factor de carga', 24, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:26:38', 1),
('__ENTITY__', 'FB', 'Factor de material', 24, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:26:48', 1),
('__ENTITY__', 'FC', 'Factor de eficiencia', 24, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:27:00', 1),
('__ENTITY__', 'FD', 'Factor de obra', 24, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:27:35', 1),
('__ENTITY__', 'LA', 'Distancia de operación', 8, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:27:58', 1),
('__ENTITY__', 'LB', 'Velocidad media de acarreo', 60, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:30:49', 1),
('__ENTITY__', 'LC', 'Velocidad media de retorno', 60, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:31:13', 1),
('__ENTITY__', 'LD', 'Velocidad de perforación', 61, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:31:32', 1),
('__ENTITY__', 'MA', 'Tiempo fijo', 62, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:31:45', 1),
('__ENTITY__', 'MB', 'Tiempo total ciclo', 62, 6, 6, '2018-04-18', '2018-04-18', '2018-04-18 16:31:59', 1);
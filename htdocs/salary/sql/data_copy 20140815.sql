/*DELETE FROM llx_p_concept WHERE entity = '__ENTITY__';*/


INSERT INTO llx_p_concept (entity, ref, detail, details, type_cod, type_mov, ref_formula, calc_oblig, calc_afp, calc_rciva, calc_agui, calc_vac, calc_indem, calc_afpvejez, calc_contrpat, calc_afpriesgo, calc_aportsol, calc_quin, print, print_input, fk_codfol, income_tax, percent) VALUES
('__ENTITY__', '101', 'SALARIO BASE', 'Salario base de calculo', 3, 2, NULL, 2, 1, 1, 1, -1, -1, 1, -1, -1, -1, -1, 2, 0, 1, 1, 1.00),
('__ENTITY__', '407', 'ANTICIPO DESCUENTO', 'ANTICIPO DESCUENTO MENSUAL', 2, 2, 'S041', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1, -1, 0.00),
('__ENTITY__', '709', 'BASE AFP RIESGO', 'BASE AFP RIESGO', 4, 2, 'S030', -1, 2, 2, -1, -1, 2, -1, -1, -1, -1, -1, 2, 0, 1, -1, 0.00),
('__ENTITY__', '705', 'BASE APORTE PATRONAL', 'BASE APORTE PATRONAL', 4, 2, 'S001', -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 2, 0, 1, -1, 0.00),
('__ENTITY__', '403', 'AFP VEJEZ', 'AFP VEJEZ', 2, 2, 'S011', -1, -1, 1, -1, -1, -1, -1, -1, -1, -1, -1, 1, 0, 1, -1, 0.00),
('__ENTITY__', '402', 'AFP RIESGO', 'AFP RIESGO', 2, 2, 'S010', -1, -1, -1, -1, -1, -1, -1, -1, 1, -1, -1, 1, 0, 1, -1, 0.00),
('__ENTITY__', '706', 'BASE AFP APORT. SOLIDARIO', 'APORTE SOLIDARIO', 4, 2, 'S030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 1, -1, 0.00),
('__ENTITY__', '405', 'AFP APORTE SOLIDARIO', 'APORTE SOLIDARIO DESCUENTO', 2, 2, 'S020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '503', 'BASICO', 'SUELDO BASICO', 1, 2, 'S002', -1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, -1, 0.00),
('__ENTITY__', '504', 'OTROS BONOS', 'OTROS BONOS ASIGNADOS AL EMPLEADO', 1, 2, 'S003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '507', 'BONO ANTIGUEDAD', 'PARA EL CALCULO DEL BONO DE ANTIGUEDAD', 1, 2, 'S005', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '102', 'BASE TOTAL TIEMPO TRABAJADO', 'TOTAL AÑOS DE ANTIGUEDAD', 3, 3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 1, -1, 0.00),
('__ENTITY__', '506', 'DOMINICALES', 'PAGO DE DOMINICALES', 1, 2, 'S004', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '712', 'BASE AFP VEJEZ', 'BASE PARA CALCULO AFP VEJEZ', 4, 2, 'S030', -1, 2, 1, -1, -1, 2, 1, -1, -1, -1, -1, 2, 0, 1, -1, 0.00),
('__ENTITY__', '713', 'BASE AFP COMISION', 'BASE DE CALCULO PARA AFP COMISION', 4, 2, 'S030', -1, 1, 2, -1, -1, 2, 2, -1, -1, -1, -1, 2, 0, 1, -1, 0.00),
('__ENTITY__', '404', 'AFP COMISION', 'DESCUENTO AFP COMISION', 2, 2, 'S012', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '103', 'BASE TOTAL GANADO', 'BASE DE CALCULO TOTAL GANADO SUMANDO BASICO, BONOS, EXTRAS', 3, 2, 'S030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '714', 'BASE RC-IVA', 'BASE DE CALCULO DEL RC-IVA EMPLEADO', 4, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 0, 1, -1, 0.00),
('__ENTITY__', '406', 'RC-IVA', 'RC-IVA DESCUENTO', 2, 2, 'S040', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00),
('__ENTITY__', '505', 'HORAS EXTRAS', 'REGISTRA LAS HORAS EXTRAS DE EMPLEADOS', 1, 2, 'S009', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, 1, -1, 0.00);

/*DELETE FROM llx_p_formulas WHERE entity = '__ENTITY__'; */

INSERT INTO llx_p_formulas (entity, ref, detail, state) VALUES
('__ENTITY__', 'S001', 'Base Total Ganado', 1),
('__ENTITY__', 'S010', 'AFP RIESGO', 1),
('__ENTITY__', 'S020', 'APORTE SOLIDARIO', 1),
('__ENTITY__', 'S002', 'BASICO', 1),
('__ENTITY__', 'S003', 'OTROS BONOS', 1),
('__ENTITY__', 'S005', 'BASE BONO ANTIGUEDAD', 1),
('__ENTITY__', 'S006', 'CALCULO TOTAL DIAS TRABAJADOS', 1),
('__ENTITY__', 'S007', 'BONO ANTIGUEDAD', 1),
('__ENTITY__', 'S004', 'DOMINICALES', 1),
('__ENTITY__', 'S011', 'AFP VEJEZ', 1),
('__ENTITY__', 'S012', 'AFP COMISION', 1),
('__ENTITY__', 'S030', 'TOTAL GANADO', 1),
('__ENTITY__', 'S040', 'RC-IVA DESCUENTO', 1),
('__ENTITY__', 'S041', 'ANTICIPO DESCUENTO', 1),
('__ENTITY__', 'S009', 'HORAS EXTRAS', 1);

/*DELETE FROM llx_p_formulas_det AS fd INNER JOIN llx_p_formulas AS f ON fd.fk_formula = f.rowid WHERE f.entity = '__ENTITY__';*/

INSERT INTO llx_p_formulas_det (entity, ref_formula, fk_operator, ref, type, changefull, andor, sequen, state) VALUES
('__ENTITY__', 'S001', 1, '', 'p_users', 'basic', 0, 1, 0),
('__ENTITY__', 'S001', 1, '', 'p_concept', '1|101', 0, 2, 0),
('__ENTITY__', 'S001', 5, '', 'p_concept', '1|101', 3, 3, 0),
('__ENTITY__', 'S010', 1, '', 'p_generic_table', '1|001|5', 1, 1, 0),
('__ENTITY__', 'S010', 3, '', 'p_concept', '1|709', 3, 2, 0),
('__ENTITY__', 'S010', 4, '', 'valor', '100', 3, 3, 0),
('__ENTITY__', 'S020', 1, '', 'p_generic_table', '1|003|8', 1, 1, 0),
('__ENTITY__', 'S020', 3, '', 'p_concept', '1|706', 1, 2, 0),
('__ENTITY__', 'S020', 4, '', 'valor', '100', 3, 3, 0),
('__ENTITY__', 'S002', 1, '', 'p_users', 'basic', 3, 1, 0),
('__ENTITY__', 'S002', 5, '', 'p_concept', '1|503', -1, 2, 0),
('__ENTITY__', 'S002', 5, '', 'p_concept', '1|101', 3, 3, 0),
('__ENTITY__', 'S003', 5, '', 'p_concept', '1|504', 3, 1, 0),
('__ENTITY__', 'S005', 1, '', 'p_concept', '1|101', 1, 1, 0),
('__ENTITY__', 'S005', 3, '', 'p_generic_table', '1|004|6', 3, 2, 0),
('__ENTITY__', 'S006', 1, '', 'p_users', 'date_ini', 1, 1, 0),
('__ENTITY__', 'S005', 3, '', 'p_users', 'basic', -1, 3, 0),
('__ENTITY__', 'S005', 1, '', 'formula', 'S005()', 3, 4, 0),
('__ENTITY__', 'S006', 1, '', 'formula', 'diastrab()', 3, 2, 0),
('__ENTITY__', 'S007', 1, '', 'p_concept', '1|507', 1, 1, 0),
('__ENTITY__', 'S007', 3, '', 'p_generic_table', '1|004|6', 1, 2, 0),
('__ENTITY__', 'S007', 4, '', 'valor', '100', -1, 3, 0),
('__ENTITY__', 'S007', 1, '', 'p_concept', '1|102', 1, 4, 0),
('__ENTITY__', 'S005', 3, '', 'p_generic_table', '1|004|6', -1, 5, 0),
('__ENTITY__', 'S005', 1, '', 'formula', 'S_diastrab()', 1, 6, 0),
('__ENTITY__', 'S005', 3, '', 'p_generic_table', '1|004|6', 1, 7, 0),
('__ENTITY__', 'S005', 4, '', 'valor', '360', 1, 8, 0),
('__ENTITY__', 'S005', 1, '', 'p_generic_table', '1|004|6', 1, 9, 0),
('__ENTITY__', 'S005', 3, '', 'formula', 'S005()', 1, 10, 0),
('__ENTITY__', 'S005', 4, '', 'valor', '100', 3, 11, 0),
('__ENTITY__', 'S004', 5, '', 'p_concept', '1|506', 3, 1, 0),
('__ENTITY__', 'S011', 1, '', 'p_generic_table', '1|003|4', -1, 1, 0),
('__ENTITY__', 'S011', 3, '', 'p_concept', '1|712', 1, 2, 0),
('__ENTITY__', 'S011', 4, '', 'valor', '100', 3, 3, 0),
('__ENTITY__', 'S012', 1, '', 'p_generic_table', '1|003|6', -1, 1, 0),
('__ENTITY__', 'S012', 3, '', 'p_concept', '1|713', 1, 2, 0),
('__ENTITY__', 'S012', 4, '', 'valor', '100', 3, 3, 0),
('__ENTITY__', 'S030', 1, '', 'p_concept', '1|503', 1, 1, 0),
('__ENTITY__', 'S030', 1, '', 'p_concept', '1|507', 1, 2, 0),
('__ENTITY__', 'S030', 1, '', 'p_concept', '1|506', 1, 3, 0),
('__ENTITY__', 'S030', 1, '', 'p_concept', '1|504', 1, 4, 0),
('__ENTITY__', 'S040', 5, '', 'p_concept', '1|406', 3, 1, 0),
('__ENTITY__', 'S041', 5, '', 'p_concept', '1|407', 3, 1, 0),
('__ENTITY__', 'S009', 5, '', 'p_concept', '1|505', 3, 1, 0);


DELETE FROM llx_p_generic_field;

/*INSERT INTO llx_p_generic_field (rowid, fk_generic_table, sequen, field_value) VALUES
(1, 3, 1, '3'),
(2, 4, 1, '1.71'),
(3, 5, 1, '10'),
(4, 6, 1, '60'),
(5, 7, 1, '3'),
(6, 10, 1, '13'),
(7, 11, 1, '4'),
(8, 12, 1, '13'),
(9, 13, 1, '001'),
(10, 16, 1, '001'),
(11, 17, 1, '10'),
(12, 18, 1, '1.71'),
(13, 19, 1, '0.5'),
(14, 20, 1, '60'),
(15, 21, 1, '0.5'),
(16, 24, 1, '001'),
(17, 25, 1, '2'),
(18, 26, 1, '4'),
(19, 27, 1, '5'),
(20, 24, 2, '002'),
(21, 25, 2, '5'),
(22, 26, 2, '7'),
(23, 27, 2, '11'),
(24, 24, 3, '003'),
(25, 25, 3, '8'),
(26, 26, 3, '10'),
(27, 27, 3, '18'),
(28, 24, 4, '004'),
(29, 25, 4, '11'),
(30, 26, 4, '14'),
(31, 27, 4, '26'),
(32, 24, 5, '005'),
(33, 25, 5, '15'),
(34, 26, 5, '19'),
(35, 27, 5, '34'),
(36, 24, 6, '006'),
(37, 25, 6, '20'),
(38, 26, 6, '24'),
(39, 27, 6, '42'),
(40, 24, 7, '007'),
(41, 25, 7, '25'),
(42, 26, 7, '99'),
(43, 27, 7, '50'),
(44, 30, 1, '001'),
(45, 31, 1, '1200');
*/

INSERT INTO llx_p_generic_field (generic_table_ref, sequen, field_value) VALUES
('001|4', 1, '3'),
('001|5', 1, '1.71'),
('001|6', 1, '10'),
('001|7', 1, '60'),
('001|8', 1, '3'),
('002|3', 1, '13'),
('002|4', 1, '4'),
('002|5', 1, '13'),
('001|3', 1, '001'),
('003|3', 1, '001'),
('003|4', 1, '10'),
('003|5', 1, '1.71'),
('003|6', 1, '0.5'),
('003|7', 1, '60'),
('003|8', 1, '0.5'),
('004|3', 1, '001'),
('004|4', 1, '2'),
('004|5', 1, '4'),
('004|6', 1, '5'),
('004|3', 2, '002'),
('004|4', 2, '5'),
('004|5', 2, '7'),
('004|6', 2, '11'),
('004|3', 3, '003'),
('004|4', 3, '8'),
('004|5', 3, '10'),
('004|6', 3, '18'),
('004|3', 4, '004'),
('004|4', 4, '11'),
('004|5', 4, '14'),
('004|6', 4, '26'),
('004|3', 5, '005'),
('004|4', 5, '15'),
('004|5', 5, '19'),
('004|6', 5, '34'),
('004|3', 6, '006'),
('004|4', 6, '20'),
('004|5', 6, '24'),
('004|6', 6, '42'),
('004|3', 7, '007'),
('004|4', 7, '25'),
('004|5', 7, '99'),
('004|6', 7, '50'),
('005|3', 1, '001'),
('005|4', 1, '1200');


/*DELETE FROM llx_p_generic_table WHERE entity = '__ENTITY__';; ;*/

/*INSERT INTO llx_p_generic_table (rowid, entity, table_cod, table_name, field_name, sequen, limits, type_value, state) VALUES
(1, '__ENTITY__', '001', 'APORTE PATRONAL', 'Sucursal', 1, 2, -1, 1),
(2, '__ENTITY__', '001', 'APORTE PATRONAL', 'MES/AÑO', 2, 2, 3, 1),
(3, '__ENTITY__', '001', 'APORTE PATRONAL', '% PRO VIVIENDA', 4, 2, 3, 0),
(4, '__ENTITY__', '001', 'APORTE PATRONAL', '% AFP RIESGO', 5, 2, 3, 0),
(5, '__ENTITY__', '001', 'APORTE PATRONAL', '% CNS SEGURO SOCIAL', 6, 2, 3, 0),
(6, '__ENTITY__', '001', 'APORTE PATRONAL', 'TOPE SMN', 7, 2, 3, 0),
(7, '__ENTITY__', '001', 'APORTE PATRONAL', '% APORTE ADICIONAL', 8, 2, 3, 0),
(8, '__ENTITY__', '002', 'RC-IVA', 'Sucursal', 1, 0, 0, 0),
(9, '__ENTITY__', '002', 'RC-IVA', 'MES/AÑO', 2, 0, 0, 0),
(10, '__ENTITY__', '002', 'RC-IVA', '% Desc IVA', 3, 0, 0, 0),
(11, '__ENTITY__', '002', 'RC-IVA', 'QTD Sal Min.', 4, 0, 0, 0),
(12, '__ENTITY__', '002', 'RC-IVA', '% Sal Minimo', 5, 0, 0, 0),
(13, '__ENTITY__', '001', 'APORTE PATRONAL', 'Secuencia', 3, 2, 3, 0),
(14, '__ENTITY__', '003', 'AFP', 'SUCURSAL', 1, 2, 3, 0),
(15, '__ENTITY__', '003', 'AFP', 'MES/ANIO', 2, 2, -1, 0),
(16, '__ENTITY__', '003', 'AFP', 'SECUENCIA', 3, 2, -1, 0),
(17, '__ENTITY__', '003', 'AFP', '% VEJEZ', 4, 2, 3, 0),
(18, '__ENTITY__', '003', 'AFP', '%RIESGO COMUN', 5, 2, 3, 0),
(19, '__ENTITY__', '003', 'AFP', '% COMISION', 6, 2, 3, 0),
(20, '__ENTITY__', '003', 'AFP', 'TOPE SMN', 7, 2, 3, 0),
(21, '__ENTITY__', '003', 'AFP', '% APORTE SOLIDARIO', 8, 2, 3, 0),
(22, '__ENTITY__', '004', 'BONO ANTIGUEDAD', 'SUCURSAL', 1, 1, -1, 0),
(23, '__ENTITY__', '004', 'BONO ANTIGUEDAD', 'MES/ANIO', 2, 1, -1, 0),
(24, '__ENTITY__', '004', 'BONO ANTIGUEDAD', 'SECUENCIA', 3, 1, -1, 0),
(25, '__ENTITY__', '004', 'BONO ANTIGUEDAD', 'ANIO DE', 4, 1, 1, 0),
(26, '__ENTITY__', '004', 'BONO ANTIGUEDAD', 'A ANIO', 5, 1, 2, 0),
(27, '__ENTITY__', '004', 'BONO ANTIGUEDAD', '% BONO ANTIGUEDAD', 6, 1, 3, 0),
(28, '__ENTITY__', '005', 'SALARIO MINIMO', 'SUCURSAL', 1, 2, -1, 0),
(29, '__ENTITY__', '005', 'SALARIO MINIMO', 'MES/ANIO', 2, 2, -1, 0),
(30, '__ENTITY__', '005', 'SALARIO MINIMO', 'SECUENCIA', 3, 2, -1, 0),
(31, '__ENTITY__', '005', 'SALARIO MINIMO', 'SALARIO MINIMO', 4, 2, 3, 0);
*/

INSERT INTO llx_p_generic_table (entity, ref, table_cod, table_name, field_name, sequen, limits, type_value, state) VALUES
('__ENTITY__', '001|1', '001', 'APORTE PATRONAL', 'Sucursal', 1, 2, -1, 1),
('__ENTITY__', '001|2', '001', 'APORTE PATRONAL', 'MES/AÑO', 2, 2, 3, 1),
('__ENTITY__', '001|4', '001', 'APORTE PATRONAL', '% PRO VIVIENDA', 4, 2, 3, 0),
('__ENTITY__', '001|5', '001', 'APORTE PATRONAL', '% AFP RIESGO', 5, 2, 3, 0),
('__ENTITY__', '001|6', '001', 'APORTE PATRONAL', '% CNS SEGURO SOCIAL', 6, 2, 3, 0),
('__ENTITY__', '001|7', '001', 'APORTE PATRONAL', 'TOPE SMN', 7, 2, 3, 1),
('__ENTITY__', '001|8', '001', 'APORTE PATRONAL', '% APORTE ADICIONAL', 8, 2, 3, 0),
('__ENTITY__', '002|1', '002', 'RC-IVA', 'Sucursal', 1, 0, 0, 0),
('__ENTITY__', '002|2', '002', 'RC-IVA', 'MES/AÑO', 2, 0, 0, 0),
('__ENTITY__', '002|3', '002', 'RC-IVA', '% Desc IVA', 3, 0, 0, 0),
('__ENTITY__', '002|4', '002', 'RC-IVA', 'QTD Sal Min.', 4, 0, 0, 0),
('__ENTITY__', '002|5', '002', 'RC-IVA', '% Sal Minimo', 5, 0, 0, 0),
('__ENTITY__', '001|3', '001', 'APORTE PATRONAL', 'Secuencia', 3, 2, 3, 1),
('__ENTITY__', '003|1', '003', 'AFP', 'SUCURSAL', 1, 2, 3, 0),
('__ENTITY__', '003|2', '003', 'AFP', 'MES/ANIO', 2, 2, -1, 0),
('__ENTITY__', '003|3', '003', 'AFP', 'SECUENCIA', 3, 2, -1, 0),
('__ENTITY__', '003|4', '003', 'AFP', '% VEJEZ', 4, 2, 3, 0),
('__ENTITY__', '003|5', '003', 'AFP', '%RIESGO COMUN', 5, 2, 3, 0),
('__ENTITY__', '003|6', '003', 'AFP', '% COMISION', 6, 2, 3, 0),
('__ENTITY__', '003|7', '003', 'AFP', 'TOPE SMN', 7, 2, 3, 0),
('__ENTITY__', '003|8', '003', 'AFP', '% APORTESOLIDARIO', 8, 2, 3, 0),
('__ENTITY__', '004|1', '004', 'BONO ANTIGUEDAD', 'SUCURSAL', 1, 1, -1, 0),
('__ENTITY__', '004|2', '004', 'BONO ANTIGUEDAD', 'MES/ANIO', 2, 1, -1, 0),
('__ENTITY__', '004|3', '004', 'BONO ANTIGUEDAD', 'SECUENCIA', 3, 1, -1, 0),
('__ENTITY__', '004|4', '004', 'BONO ANTIGUEDAD', 'ANIO DE', 4, 1, 1, 1),
('__ENTITY__', '004|5', '004', 'BONO ANTIGUEDAD', 'A ANIO', 5, 1, 2, 0),
('__ENTITY__', '004|6', '004', 'BONO ANTIGUEDAD', '% BONO ANTIGUEDAD', 6, 1, 3, 0),
('__ENTITY__', '005|1', '005', 'SALARIO MINIMO', 'SUCURSAL', 1, 2, -1, 0),
('__ENTITY__', '005|2', '005', 'SALARIO MINIMO', 'MES/ANIO', 2, 2, -1, 0),
('__ENTITY__', '005|3', '005', 'SALARIO MINIMO', 'SECUENCIA', 3, 2, -1, 0),
('__ENTITY__', '005|4', '005', 'SALARIO MINIMO', 'SALARIO MINIMO', 4, 2, 3, 0),
('__ENTITY__', '006|1', '006', 'APORTE SOLIDARIO', 'SUCURSAL', 1, 1, -1, 1),
('__ENTITY__', '006|2', '006', 'APORTE SOLIDARIO', 'MES/ANIO', 2, 1, -1, 1),
('__ENTITY__', '006|3', '006', 'APORTE SOLIDARIO', 'SECUENCIA', 3, 1, -1, 1),
('__ENTITY__', '006|4', '006', 'APORTE SOLIDARIO', 'DESDE', 4, 1, 1, 1),
('__ENTITY__', '006|5', '006', 'APORTE SOLIDARIO', 'HASTA', 5, 1, 2, 1),
('__ENTITY__', '006|6', '006', 'APORTE SOLIDARIO', '% APORTE SOLIDARIO', 6, 1, 3, 1);

/*DELETE FROM llx_p_type_fol  WHERE entity = '__ENTITY__';*/

INSERT INTO llx_p_type_fol (rowid, entity, ref, detail, details, state) VALUES
(1, 1, 'PSAL', 'PLANILLA DE SUELDO MENSUAL', 'Planilla de sueldos mensuales', 1),
(2, 1, 'AGUI', 'Aguinaldos', 'Pago de Aguinaldos', 1);

DELETE FROM llx_p_type_fol_det ;

INSERT INTO llx_p_type_fol_det (rowid, fk_type_fol, sequen, detail, formula, state, details) VALUES
(1, 1, 10, 'Cargar Empleados', 's_cargamie', 1, 'Carga de empleados'),
(2, 1, 20, 'Calcula bases', 's_calcbase', 1, 'Calacula las bases definidas en los conceptos'),
(3, 1, 30, 'Calcula entradas - rendimientos', 's_calcrend', 1, 'Calcula los rendimientos por cada persona'),
(4, 1, 40, 'Calcula descuentos', 's_calcdesc', 1, 'Calcula los descuentos para cada persona');

DELETE llx_p_operator;

INSERT INTO llx_p_operator (rowid, detail, operator, type, state) VALUES
(1, 'Sumar', '+', 1, 1),
(2, 'Restar', '-', 1, 1),
(3, 'Multiplicar', '*', 1, 1),
(4, 'Dividir', '/', 1, 1),
(5, 'Sumar Columna', 'sum()', 2, 1);

DELETE llx_p_civility;

INSERT INTO llx_p_civility (rowid, code, label, active) VALUES
(1, 'SI', 'Single', 1, 1),
(2, 'MA', 'Married', 1, 1),
(3, 'DI', 'Divorced', 1, 1),
(4, 'WI', 'Widower', 1, 1);


DELETE llx_p_blood_type;

INSERT INTO llx_p_blood_type (rowid, code, label, active) VALUES
(1, 'O+', 'O+', 1),
(2, 'O-', 'O-', 1),
(3, 'A+', 'A+', 1),
(4, 'A-', 'A-', 1),
(5, 'B+', 'B+', 1),
(6, 'B-', 'B-', 1),
(7, 'AB+', 'AB+', 1),
(8, 'AB-', 'AB-', 1);

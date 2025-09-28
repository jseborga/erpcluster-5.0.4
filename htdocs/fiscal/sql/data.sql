INSERT INTO llx_c_type_facture (fk_pays, code, label, detail, type, type_fact, type_value, nit_required, active) VALUES
(52, 'STDC', 'Factura Standard de Compra', 'Factura standard por la compra de productos', 0, 0, 0, 1, 1),
(52, 'STDV', 'Factura Standard de Venta', 'Factura estandard por la venta de productos', 0, 1, 0, 1, 1),
(52, 'RETPROD', 'Factura retencion compra productos', 'Factura con retención por compra de productos', 0, 0, 0, 0, 1),
(52, 'RETSERV', 'Factura retencion compra servicios', 'Factura con retencion por compra de servicios', 0, 0, 0, 0, 1);

INSERT INTO llx_c_type_tva (fk_pays, code, label, active) VALUES
(52, 'IVA', 'Impuesto IVA', 1),
(52, 'IT', 'Impuesto Transaciones', 1),
(52, 'IUE', 'Impuesto Utilidades Retencion Servicios', 1),
(52, 'ICE', 'Impuesto al Consumo especifico', 1),
(52, 'RCIVA', 'Regimen Complementario IVA', 1);


INSERT INTO llx_tva_def (fk_pays, code_facture, code_tva, taux, register_mode, note, active, accountancy_code) VALUES
(52, 'STDC', 'IVA', 13, 1, '', 1, ''),
(52, 'STDV', 'IVA', 13, 1, '', 1, ''),
(52, 'STDV', 'IT', 3, 1, '', 1, ''),
(52, 'RETPROD', 'IUE', 5, 1, '', 0, ''),
(52, 'RETPROD', 'IT', 3, 1, '', 0, ''),
(52, 'RETSERV', 'IUE', 12.5, 1, '', 0, ''),
(52, 'RETSERV', 'IT', 3, 1, '', 0, '');

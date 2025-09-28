INSERT INTO llx_c_assets_been (entity, code, label, active) VALUES
(__ENTITY__, -1, 'Baja', 1),
(__ENTITY__, 1, 'Nuevo', 1),
(__ENTITY__, 2, 'Bueno', 1),
(__ENTITY__, 3, 'Mantenimiento', 1),
(__ENTITY__, 4, 'Reparacion', 1),
(__ENTITY__, 5, 'Desperfecto', 1);

INSERT INTO llx_c_assets_method (entity, ref, label, active) VALUES
(__ENTITY__, 'LIN', 'Lineal', 1);

INSERT INTO llx_c_assets_patrim (entity, code, label, active) VALUES
(__ENTITY__, 'AF', 'Activo Fijo', 1);

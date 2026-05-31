USE medtime;

-- ─────────────────────────────────────────────
-- Contraseña de todos los usuarios: Medtime123
-- ─────────────────────────────────────────────
INSERT INTO usuario (nombre, apellidos, email, telefono, contrasena_hash, rol, activo) VALUES
('Daniel', 'Fernandez Rodriguez', 'daniel@medtime.com', '600111222', '$2y$12$jUvqFECgfdNjxjVASqU.R.tOl5JvxHSzNBhTsHgwsOdhLkT3UTG2O', 'PACIENTE',    TRUE),
('Laura',  'Gomez Martinez',      'laura.gomez@medtime.com',  '600222333', '$2y$12$LV09QjX0GLTfcMYcH0sc9.phBW70uiBnCgSp24jQ6u1uvCsSKNcQS', 'PROFESIONAL', TRUE),
('Carlos', 'Perez Lopez',         'carlos.perez@medtime.com', '600333444', '$2y$12$n7Kq0HCGmYRO2.flGZ7jNecH0ALB//QAAmROexCPqq74JpxoRPIEi', 'PROFESIONAL', TRUE),
('Admin',  'Sistema',             'admin@medtime.com',        '600000000', '$2y$12$YQny1lxi/0EeBopVqsnSIutqD6DKgapxxB.nXG5wmfMmUA63JIV26', 'ADMIN',       TRUE),
('Marta',  'Suarez Diaz',         'marta@medtime.com',        '600444555', '$2y$12$ZLzXMl.d758IwFwL/CtwnO54ZL6a34SysFhDbKEbho1.eef/UrvKy', 'PACIENTE',    TRUE);

INSERT INTO paciente (id_usuario, dni, fecha_nacimiento, num_historial) VALUES
(1, '12345678A', '2000-05-14', 'HIST-000001'),
(5, '87654321B', '1998-11-02', 'HIST-000005');

INSERT INTO profesional (id_usuario, especialidad, num_colegiado, duracion_media_consulta_min, disponible) VALUES
(2, 'Medicina General', 'COL-1001', 20, TRUE),
(3, 'Cardiología',      'COL-1002', 30, TRUE);

-- ─────────────────────────────────────────────
-- Citas con fechas dinámicas (relativas a HOY)
-- ─────────────────────────────────────────────

-- HOY — profesional 1 (Laura, Medicina General)
INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, fecha_hora_estimada, fecha_hora_real_inicio, fecha_hora_real_fin, estado, observaciones) VALUES
(1, 1, TIMESTAMP(CURDATE(), '09:00:00'), NULL,                                  TIMESTAMP(CURDATE(),'09:02:00'), TIMESTAMP(CURDATE(),'09:24:00'), 'FINALIZADA', 'Revisión general anual'),
(2, 1, TIMESTAMP(CURDATE(), '10:00:00'), NULL,                                  TIMESTAMP(CURDATE(),'09:58:00'), TIMESTAMP(CURDATE(),'10:18:00'), 'FINALIZADA', 'Primera consulta'),
(1, 1, TIMESTAMP(CURDATE(), '11:30:00'), TIMESTAMP(CURDATE(), '11:50:00'),      NULL,                            NULL,                           'CONFIRMADA', 'Seguimiento analítica'),
(2, 1, TIMESTAMP(CURDATE(), '12:30:00'), TIMESTAMP(CURDATE(), '12:50:00'),      NULL,                            NULL,                           'PENDIENTE',  'Dolor de cabeza recurrente');

-- HOY — profesional 2 (Carlos, Cardiología)
INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, fecha_hora_estimada, fecha_hora_real_inicio, fecha_hora_real_fin, estado, observaciones) VALUES
(1, 2, TIMESTAMP(CURDATE(), '10:00:00'), NULL,                                  NULL, NULL, 'CONFIRMADA', 'Revisión cardiológica rutinaria'),
(2, 2, TIMESTAMP(CURDATE(), '10:30:00'), NULL,                                  NULL, NULL, 'PENDIENTE',  'Electrocardiograma');

-- PRÓXIMOS DÍAS — profesional 1
INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, estado, observaciones) VALUES
(1, 1, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 2  DAY), '09:00:00'), 'CONFIRMADA', 'Control tensión arterial'),
(2, 1, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 3  DAY), '10:30:00'), 'PENDIENTE',  'Revisión postoperatoria'),
(1, 1, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 5  DAY), '11:00:00'), 'CONFIRMADA', 'Analítica de sangre'),
(2, 1, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 7  DAY), '09:30:00'), 'PENDIENTE',  'Revisión anual'),
(1, 1, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 10 DAY), '16:00:00'), 'CONFIRMADA', 'Seguimiento tratamiento');

-- PRÓXIMOS DÍAS — profesional 2
INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, estado, observaciones) VALUES
(1, 2, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 4  DAY), '15:00:00'), 'PENDIENTE',  'Ecocardiograma'),
(2, 2, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 6  DAY), '09:00:00'), 'CONFIRMADA', 'Revisión Holter'),
(2, 2, TIMESTAMP(DATE_ADD(CURDATE(), INTERVAL 12 DAY), '10:00:00'), 'PENDIENTE',  'Prueba de esfuerzo');

-- HISTORIAL (pasado) — para que los pacientes tengan visitas previas
INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, fecha_hora_real_inicio, fecha_hora_real_fin, estado, observaciones) VALUES
(1, 1, TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '09:00:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '09:05:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '09:22:00'), 'FINALIZADA', 'Consulta rutinaria hace un mes'),
(2, 1, TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 15 DAY), '11:00:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 15 DAY), '11:02:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 15 DAY), '11:19:00'), 'FINALIZADA', 'Seguimiento hace dos semanas'),
(1, 2, TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 60 DAY), '10:00:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 60 DAY), '10:01:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 60 DAY), '10:29:00'), 'FINALIZADA', 'Primera revisión cardiológica'),
(2, 2, TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 45 DAY), '16:00:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 45 DAY), '16:03:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 45 DAY), '16:28:00'), 'FINALIZADA', 'Control cardiológico');

INSERT INTO notificacion (id_cita, tipo, mensaje, fecha_envio, estado) VALUES
(1, 'EMAIL', 'Su cita de ayer fue completada correctamente.', DATE_SUB(NOW(), INTERVAL 1 DAY), 'ENVIADA'),
(3, 'EMAIL', 'Recordatorio: tiene una cita mañana a las 11:30 con Laura Gomez (Medicina General).', DATE_SUB(NOW(), INTERVAL 1 DAY), 'ENVIADA'),
(3, 'PUSH',  'Su cita de hoy a las 11:30 está confirmada. Hora estimada: 11:50.', DATE_SUB(NOW(), INTERVAL 2 HOUR), 'ENVIADA'),
(5, 'EMAIL', 'Recordatorio: cita de cardiología hoy a las 10:00 con Carlos Perez.', DATE_SUB(NOW(), INTERVAL 3 HOUR), 'ENVIADA');

USE medtime;

INSERT INTO usuario (nome, apelidos, email, telefono, contrasinal_hash, rol, activo) VALUES
('Daniel', 'Fernandez Rodriguez', 'daniel@medtime.com', '600111222', 'hash_daniel', 'PACIENTE', TRUE),
('Laura', 'Gomez Martinez', 'laura.gomez@medtime.com', '600222333', 'hash_laura', 'PROFESIONAL', TRUE),
('Carlos', 'Perez Lopez', 'carlos.perez@medtime.com', '600333444', 'hash_carlos', 'PROFESIONAL', TRUE),
('Admin', 'Sistema', 'admin@medtime.com', '600000000', 'hash_admin', 'ADMIN', TRUE),
('Marta', 'Suarez Diaz', 'marta@medtime.com', '600444555', 'hash_marta', 'PACIENTE', TRUE);

INSERT INTO paciente (id_usuario, dni, data_nacemento, num_historial) VALUES
(1, '12345678A', '2000-05-14', 'HIST-0001'),
(5, '87654321B', '1998-11-02', 'HIST-0002');

INSERT INTO profesional (id_usuario, especialidade, num_colexiado, duracion_media_consulta_min, dispoñible) VALUES
(2, 'Medicina Xeral', 'COL-1001', 20, TRUE),
(3, 'Cardioloxía', 'COL-1002', 30, TRUE);

INSERT INTO cita (
    id_paciente,
    id_profesional,
    data_hora_programada,
    data_hora_estimada,
    data_hora_real_inicio,
    data_hora_real_fin,
    estado,
    observacions
) VALUES
(1, 1, '2026-05-24 17:00:00', '2026-05-24 17:30:00', NULL, NULL, 'CONFIRMADA', 'Primeira consulta de medicina xeral'),
(1, 2, '2026-05-26 10:00:00', '2026-05-26 10:20:00', NULL, NULL, 'PENDENTE', 'Revisión cardiolóxica'),
(2, 1, '2026-05-25 12:00:00', '2026-05-25 12:10:00', '2026-05-25 12:12:00', '2026-05-25 12:29:00', 'FINALIZADA', 'Consulta de seguimento');

INSERT INTO notificacion (id_cita, tipo, mensaxe, estado_envio) VALUES
(1, 'EMAIL', 'A súa cita está programada para as 17:00. Hora recomendada de chegada: 17:30.', 'ENVIADA'),
(1, 'PUSH', 'Detectouse un atraso estimado de 30 minutos na súa consulta.', 'ENVIADA'),
(2, 'EMAIL', 'Recordatorio: ten unha cita cardiolóxica mañá ás 10:00.', 'ENVIADA'),
(3, 'SMS', 'A súa consulta foi completada correctamente.', 'ENVIADA');
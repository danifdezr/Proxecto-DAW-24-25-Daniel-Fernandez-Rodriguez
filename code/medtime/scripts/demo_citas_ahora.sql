-- ─────────────────────────────────────────────────────────────
--  DEMO DINÁMICA — crea las citas de HOY ancladas a NOW()
--  NO escribe fecha_hora_estimada: la calcula el código real de
--  la app (CitaModel::recalcularEstimaciones) tras inyectar esto.
--
--  Profesional 1 = Laura (Medicina General, 20 min/consulta)
--  Paciente 1     = Daniel (daniel@medtime.com)  ← veremos SU hora estimada
--  Paciente 2     = Marta  (marta@medtime.com)   ← rellena la cola
-- ─────────────────────────────────────────────────────────────

USE medtime;

-- Limpia TODAS las citas de hoy para un escenario limpio
DELETE FROM cita WHERE DATE(fecha_hora_programada) = CURDATE();

-- 1) Marta — cita anterior FINALIZADA que se alargó (45 min en vez de 20).
--    Es la que "arrastra" el retraso de toda la cola.
INSERT INTO cita
  (id_paciente, id_profesional, fecha_hora_programada, fecha_hora_real_inicio, fecha_hora_real_fin, estado, observaciones)
VALUES
  (2, 1, NOW() - INTERVAL 50 MINUTE, NOW() - INTERVAL 50 MINUTE, NOW() - INTERVAL 5 MINUTE, 'FINALIZADA', 'Consulta que se alargó');

-- 2) Marta — cita que se está atendiendo AHORA MISMO (EN_CONSULTA).
INSERT INTO cita
  (id_paciente, id_profesional, fecha_hora_programada, fecha_hora_real_inicio, estado, observaciones)
VALUES
  (2, 1, NOW() - INTERVAL 30 MINUTE, NOW() - INTERVAL 5 MINUTE, 'EN_CONSULTA', 'Paciente en consulta ahora');

-- 3) Marta — cita CONFIRMADA esperando en sala (delante de Daniel).
INSERT INTO cita
  (id_paciente, id_profesional, fecha_hora_programada, estado, observaciones)
VALUES
  (2, 1, NOW() - INTERVAL 10 MINUTE, 'CONFIRMADA', 'Esperando en sala');

-- 4) Daniel — SU cita: programada para dentro de poco (sigue siendo futura,
--    por eso aparece como "próxima cita") pero la cola la empuja más tarde.
INSERT INTO cita
  (id_paciente, id_profesional, fecha_hora_programada, estado, observaciones)
VALUES
  (1, 1, NOW() + INTERVAL 10 MINUTE, 'CONFIRMADA', 'Revisión general');

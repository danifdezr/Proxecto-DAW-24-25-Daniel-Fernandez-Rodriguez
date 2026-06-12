#!/usr/bin/env bash
# ─────────────────────────────────────────────────────────────
#  DEMO HORA ESTIMADA — orquestador de un solo comando
#
#  Hace TODO el escenario de golpe:
#    1) Siembra las citas de HOY ancladas a la hora actual (NOW())
#    2) Ejecuta el algoritmo REAL de la app (recalcularEstimaciones)
#    3) Muestra la cola resultante con la hora estimada calculada
#
#  Como todo va anclado a NOW()/CURDATE(), sirve a cualquier hora:
#  vuelve a lanzarlo justo antes de presentar (p.ej. a las 13:00)
#  y regenera el escenario fresco sobre ese instante.
#
#  Uso (con los contenedores levantados), desde code/medtime/:
#      ./scripts/demo.sh                # profesional 1, 20 min/consulta
#      ./scripts/demo.sh 1 20           # explícito
#
#  Luego entra en la web como  daniel@medtime.com  (pass: Medtime123)
#  para ver SU hora estimada y el "2 pacientes delante de ti".
# ─────────────────────────────────────────────────────────────
set -euo pipefail

# Nos situamos en code/medtime/ pase lo que pase
cd "$(dirname "$0")/.."

PROF="${1:-1}"   # id del profesional (1 = Laura, Medicina General)
DUR="${2:-20}"   # duración media de consulta en minutos

DB=(docker compose exec -T mariadb mariadb -umedtime -pmedtime medtime)

echo "▶ 1/3  Sembrando escenario de HOY (anclado a la hora actual)…"
"${DB[@]}" < scripts/demo_citas_ahora.sql

echo "▶ 2/3  Ejecutando el algoritmo real (CitaModel::recalcularEstimaciones)…"
docker compose exec -T web php scripts/recalcular.php "$PROF" "$DUR"

echo "▶ 3/3  Estado final de la cola del profesional $PROF:"
"${DB[@]}" -t -e "
SELECT id_cita,
       TIME(fecha_hora_programada) AS programada,
       TIME(fecha_hora_estimada)   AS estimada,
       estado,
       CASE id_paciente WHEN 1 THEN 'Daniel'
                        WHEN 2 THEN 'Marta'
                        ELSE CAST(id_paciente AS CHAR) END AS paciente
FROM cita
WHERE id_profesional = ${PROF}
  AND DATE(fecha_hora_programada) = CURDATE()
ORDER BY fecha_hora_programada;"

echo
echo "✅ Listo. Entra como daniel@medtime.com (pass: Medtime123)."
echo "   Daniel verá su hora estimada empujada por la cola y '2 pacientes delante'."

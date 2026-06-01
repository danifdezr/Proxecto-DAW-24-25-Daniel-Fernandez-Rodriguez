<?php

namespace app\models;

use app\models\vo\CitaVo;
use PDO;
use PDOException;

class CitaModel extends Model
{
    private static function sqlConProfesional(): string
    {
        return "SELECT c.id_cita, c.id_paciente, c.id_profesional,
                       DATE(c.fecha_hora_programada)  AS fecha_cita,
                       TIME(c.fecha_hora_programada)  AS hora_cita,
                       c.fecha_hora_estimada,
                       c.estado,
                       c.observaciones,
                       u.nombre    AS profesional_nombre,
                       u.apellidos AS profesional_apellidos,
                       pr.especialidad
                FROM cita c
                INNER JOIN profesional pr ON c.id_profesional = pr.id_profesional
                INNER JOIN usuario     u  ON pr.id_usuario    = u.id_usuario";
    }

    private static function sqlConPaciente(): string
    {
        return "SELECT c.id_cita, c.id_paciente, c.id_profesional,
                       DATE(c.fecha_hora_programada)  AS fecha_cita,
                       TIME(c.fecha_hora_programada)  AS hora_cita,
                       c.fecha_hora_estimada,
                       c.fecha_hora_real_inicio,
                       c.fecha_hora_real_fin,
                       c.estado,
                       c.observaciones,
                       u.nombre    AS paciente_nombre,
                       u.apellidos AS paciente_apellidos
                FROM cita c
                INNER JOIN paciente p ON c.id_paciente = p.id_paciente
                INNER JOIN usuario  u ON p.id_usuario  = u.id_usuario";
    }

    //Consultas genéricas

    public static function getCitaById(int $id): ?CitaVo
    {
        $sql = "SELECT id_cita, id_paciente, id_profesional,
                       DATE(fecha_hora_programada) AS fecha_cita,
                       TIME(fecha_hora_programada) AS hora_cita,
                       estado, observaciones, fecha_hora_estimada
                FROM cita WHERE id_cita = :id";

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return isset($row) && $row ? self::rowToVo($row) : null;
    }

    public static function iniciarCita(int $idCita): bool
    {
        $ahora = date('Y-m-d H:i:s');
        $ok    = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare(
                "UPDATE cita SET estado = 'EN_CONSULTA', fecha_hora_real_inicio = :ahora
                 WHERE id_cita = :id"
            );
            $stmt->bindValue(':ahora', $ahora);
            $stmt->bindValue(':id',    $idCita, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function finalizarCita(int $idCita): bool
    {
        $ahora = date('Y-m-d H:i:s');
        $ok    = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare(
                "UPDATE cita SET estado = 'FINALIZADA', fecha_hora_real_fin = :ahora
                 WHERE id_cita = :id"
            );
            $stmt->bindValue(':ahora', $ahora);
            $stmt->bindValue(':id',    $idCita, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function recalcularEstimaciones(int $idProfesional, string $fecha, int $duracionMin): void
    {
        try {
            $db = self::getConnection();

            $stmt = $db->prepare(
                "SELECT id_cita, fecha_hora_programada, fecha_hora_real_fin, estado
                 FROM cita
                 WHERE id_profesional = :id
                   AND DATE(fecha_hora_programada) = :fecha
                   AND estado NOT IN ('CANCELADA')
                 ORDER BY fecha_hora_programada ASC"
            );
            $stmt->bindValue(':id',    $idProfesional, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $fecha,         PDO::PARAM_STR);
            $stmt->execute();
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($appointments)) {
                return;
            }

            // timestamp de cuándo el profesional queda libre
            $cursor  = null; 

            // [id_cita => nueva_estimada | null]
            $updates = [];   

            foreach ($appointments as $apt) {
                $tProg = strtotime($apt['fecha_hora_programada']);

                if ($apt['estado'] === 'FINALIZADA' && $apt['fecha_hora_real_fin']) {
                    $tFin  = strtotime($apt['fecha_hora_real_fin']);
                    $cursor = max($tProg + $duracionMin * 60, $tFin);
                } else {
                    if ($cursor !== null) {
                        $tEst = max($tProg, $cursor);
                        $updates[$apt['id_cita']] = $tEst > $tProg
                            ? date('Y-m-d H:i:s', $tEst)
                            : null;
                        $cursor = $tEst + $duracionMin * 60;
                    } else {
                        $updates[$apt['id_cita']] = null;
                        $cursor = $tProg + $duracionMin * 60;
                    }
                }
            }

            $upd = $db->prepare("UPDATE cita SET fecha_hora_estimada = :est WHERE id_cita = :id");
            foreach ($updates as $idCita => $estimada) {
                $upd->bindValue(':est', $estimada);
                $upd->bindValue(':id',  $idCita, PDO::PARAM_INT);
                $upd->execute();
            }
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }
    }

    public static function getCitaEnConsulta(int $idProfesional): ?array
    {
        $sql = self::sqlConPaciente() .
               " WHERE c.id_profesional = :id
                   AND c.estado = 'EN_CONSULTA'
                   AND DATE(c.fecha_hora_programada) = CURDATE()
                 LIMIT 1";

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $idProfesional, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
            $row = null;
        } finally {
            $db = null;
        }

        return $row;
    }

    public static function getPacientesDelante(int $idProfesional, string $fecha, string $hora): int
    {
        $sql = "SELECT COUNT(*) AS total
                FROM cita
                WHERE id_profesional = :id
                  AND DATE(fecha_hora_programada) = :fecha
                  AND TIME(fecha_hora_programada) < :hora
                  AND estado NOT IN ('FINALIZADA', 'CANCELADA')";

        $total = 0;
        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',    $idProfesional, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $fecha,         PDO::PARAM_STR);
            $stmt->bindValue(':hora',  $hora,          PDO::PARAM_STR);
            $stmt->execute();
            $total = (int)$stmt->fetchColumn();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $total;
    }

    public static function cambiarEstadoCita(int $idCita, string $estado): bool
    {
        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare("UPDATE cita SET estado = :estado WHERE id_cita = :id");
            $stmt->bindValue(':estado', $estado);
            $stmt->bindValue(':id', $idCita, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    // Consultas para el paciente

    public static function getProximaCitaPaciente(int $idPaciente): ?array
    {
        $ahora = date('Y-m-d H:i:s');

        $sql = self::sqlConProfesional() .
               " WHERE c.id_paciente = :id
                   AND c.fecha_hora_programada >= :ahora
                   AND c.estado NOT IN ('CANCELADA', 'FINALIZADA')
                 ORDER BY c.fecha_hora_programada ASC
                 LIMIT 1";

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',    $idPaciente, PDO::PARAM_INT);
            $stmt->bindValue(':ahora', $ahora,      PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
            $row = null;
        } finally {
            $db = null;
        }

        return $row;
    }

    public static function getCitasProximasPaciente(int $idPaciente): array
    {
        $ahora = date('Y-m-d H:i:s');

        $sql = self::sqlConProfesional() .
               " WHERE c.id_paciente = :id
                   AND c.fecha_hora_programada >= :ahora
                   AND c.estado NOT IN ('CANCELADA', 'FINALIZADA')
                 ORDER BY c.fecha_hora_programada ASC";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',    $idPaciente, PDO::PARAM_INT);
            $stmt->bindValue(':ahora', $ahora,      PDO::PARAM_STR);
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    public static function getHistorialPaciente(int $idPaciente, int $limit = 5): array
    {
        $ahora = date('Y-m-d H:i:s');

        $sql = self::sqlConProfesional() .
               " WHERE c.id_paciente = :id
                   AND c.fecha_hora_programada < :ahora
                 ORDER BY c.fecha_hora_programada DESC
                 LIMIT :lim";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',    $idPaciente, PDO::PARAM_INT);
            $stmt->bindValue(':ahora', $ahora,      PDO::PARAM_STR);
            $stmt->bindValue(':lim',   $limit,      PDO::PARAM_INT);
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    public static function getCitasByPacienteConProfesional(int $idPaciente): array
    {
        $sql = self::sqlConProfesional() .
               " WHERE c.id_paciente = :id
                 ORDER BY
                   CASE WHEN c.fecha_hora_programada >= NOW() THEN 0 ELSE 1 END,
                   c.fecha_hora_programada ASC";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $idPaciente, PDO::PARAM_INT);
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    //Consultas para el profesional

    public static function getCitasByProfesionalHoy(int $idProfesional): array
    {
        $sql = self::sqlConPaciente() .
               " WHERE c.id_profesional = :id
                   AND DATE(c.fecha_hora_programada) = CURDATE()
                 ORDER BY c.fecha_hora_programada ASC";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $idProfesional, PDO::PARAM_INT);
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    public static function getCitasByProfesionalProximas(int $idProfesional, int $dias = 14): array
    {
        $sql = self::sqlConPaciente() .
               " WHERE c.id_profesional = :id
                   AND DATE(c.fecha_hora_programada) > CURDATE()
                   AND DATE(c.fecha_hora_programada) <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                 ORDER BY c.fecha_hora_programada ASC";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',   $idProfesional, PDO::PARAM_INT);
            $stmt->bindValue(':dias', $dias,          PDO::PARAM_INT);
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    public static function getCitasByProfesional(int $idProfesional, ?string $estado = null): array
    {
        $estadosValidos = ['PENDIENTE', 'CONFIRMADA', 'FINALIZADA', 'CANCELADA'];

        if ($estado !== null && !in_array($estado, $estadosValidos, true)) {
            $estado = null;
        }

        $sql = self::sqlConPaciente() .
               " WHERE c.id_profesional = :id" .
               ($estado !== null ? " AND c.estado = :estado" : "") .
               " ORDER BY
                   CASE WHEN c.fecha_hora_programada >= NOW() THEN 0 ELSE 1 END,
                   c.fecha_hora_programada ASC";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $idProfesional, PDO::PARAM_INT);
            if ($estado !== null) {
                $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
            }
            $stmt->execute();
            $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $citas;
    }

    public static function reprogramarCita(int $idCita, string $nuevaFechaHora): bool
    {
        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare(
                "UPDATE cita
                 SET fecha_hora_programada = :fh,
                     estado                = 'PENDIENTE',
                     fecha_hora_estimada   = NULL,
                     fecha_hora_real_inicio = NULL,
                     fecha_hora_real_fin   = NULL
                 WHERE id_cita = :id"
            );
            $stmt->bindValue(':fh', $nuevaFechaHora);
            $stmt->bindValue(':id', $idCita, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function getCitasByProfesionalFecha(int $idProfesional, string $fecha, ?int $excludeCitaId = null): array
    {
        $sql = "SELECT TIME(fecha_hora_programada) AS hora_cita
                FROM cita
                WHERE id_profesional = :id
                  AND DATE(fecha_hora_programada) = :fecha
                  AND estado NOT IN ('CANCELADA')";

        if ($excludeCitaId !== null) {
            $sql .= " AND id_cita != :excluir";
        }

        $rows = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id',    $idProfesional, PDO::PARAM_INT);
            $stmt->bindValue(':fecha', $fecha,         PDO::PARAM_STR);
            if ($excludeCitaId !== null) {
                $stmt->bindValue(':excluir', $excludeCitaId, PDO::PARAM_INT);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $rows;
    }

    public static function crearCita(
        int $idPaciente,
        int $idProfesional,
        string $fechaHora,
        ?string $observaciones = null
    ): int {
        $sql = "INSERT INTO cita (id_paciente, id_profesional, fecha_hora_programada, estado, observaciones)
                VALUES (:id_paciente, :id_profesional, :fecha_hora, 'PENDIENTE', :observaciones)";

        $id = 0;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_paciente',    $idPaciente,    PDO::PARAM_INT);
            $stmt->bindValue(':id_profesional', $idProfesional, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_hora',     $fechaHora,     PDO::PARAM_STR);
            $stmt->bindValue(':observaciones',  $observaciones);
            $stmt->execute();
            $id = (int) $db->lastInsertId();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $id;
    }

    public static function getCitas(): ?array
    {
        $sql = "SELECT id_cita, id_paciente, id_profesional,
                       DATE(fecha_hora_programada) AS fecha_cita,
                       TIME(fecha_hora_programada) AS hora_cita,
                       estado, observaciones, fecha_hora_estimada
                FROM cita";

        $citas = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $citas[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($citas) ? $citas : null;
    }

    private static function rowToVo(array $row): CitaVo
    {
        return new CitaVo(
            (int) $row['id_cita'],
            (int) $row['id_paciente'],
            (int) $row['id_profesional'],
            $row['fecha_cita'],
            $row['hora_cita'],
            $row['estado'],
            $row['observaciones'],
            $row['fecha_hora_estimada']
        );
    }
}

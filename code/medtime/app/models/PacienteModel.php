<?php

namespace app\models;

use app\models\vo\PacienteVo;
use PDO;
use PDOException;

class PacienteModel extends Model
{
    public static function getPacienteById(int $id): ?PacienteVo
    {
        $sql = "SELECT p.id_paciente, u.nombre, u.apellidos, u.email, u.telefono, p.dni, p.fecha_nacimiento
                FROM paciente p
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                WHERE p.id_paciente = :id";

        try {
            $db = self::getConnection();
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

    public static function getPacienteByIdUsuario(int $idUsuario): ?PacienteVo
    {
        $sql = "SELECT p.id_paciente, u.nombre, u.apellidos, u.email, u.telefono, p.dni, p.fecha_nacimiento
                FROM paciente p
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario
                WHERE p.id_usuario = :id_usuario";

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return isset($row) && $row ? self::rowToVo($row) : null;
    }

    public static function getPacientes(): ?array
    {
        $sql = "SELECT p.id_paciente, u.nombre, u.apellidos, u.email, u.telefono, p.dni, p.fecha_nacimiento
                FROM paciente p
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario";

        $pacientes = [];

        try {
            $db = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $pacientes[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($pacientes) ? $pacientes : null;
    }

    public static function getPacientesByProfesional(int $idProfesional): array
    {
        $sql = "SELECT
                    p.id_paciente,
                    u.nombre,
                    u.apellidos,
                    u.email,
                    u.telefono,
                    p.dni,
                    p.fecha_nacimiento,
                    COUNT(c.id_cita) AS total_citas,
                    MAX(CASE WHEN c.fecha_hora_programada < NOW() THEN c.fecha_hora_programada END) AS ultima_cita,
                    MIN(CASE WHEN c.fecha_hora_programada >= NOW() THEN c.fecha_hora_programada END) AS proxima_cita
                FROM cita c
                INNER JOIN paciente p ON c.id_paciente  = p.id_paciente
                INNER JOIN usuario  u ON p.id_usuario   = u.id_usuario
                WHERE c.id_profesional = :id
                GROUP BY p.id_paciente, u.nombre, u.apellidos, u.email, u.telefono, p.dni, p.fecha_nacimiento
                ORDER BY u.apellidos ASC, u.nombre ASC";

        $pacientes = [];

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id', $idProfesional, PDO::PARAM_INT);
            $stmt->execute();
            $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $pacientes;
    }

    public static function crearPaciente(
        int $idUsuario,
        string $dni,
        string $fechaNacimiento,
        string $numHistorial
    ): bool {
        $sql = "INSERT INTO paciente (id_usuario, dni, fecha_nacimiento, num_historial)
                VALUES (:id_usuario, :dni, :fecha_nacimiento, :num_historial)";

        $ok = false;

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_usuario', $idUsuario, PDO::PARAM_INT);
            $stmt->bindValue(':dni', $dni);
            $stmt->bindValue(':fecha_nacimiento', $fechaNacimiento);
            $stmt->bindValue(':num_historial', $numHistorial);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    private static function rowToVo(array $row): PacienteVo
    {
        return new PacienteVo(
            (int) $row['id_paciente'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $row['telefono'],
            $row['dni'],
            $row['fecha_nacimiento']
        );
    }
}

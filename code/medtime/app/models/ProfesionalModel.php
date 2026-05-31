<?php

namespace app\models;

use app\models\vo\ProfesionalVo;
use PDO;
use PDOException;

class ProfesionalModel extends Model
{
    private static function baseSql(): string
    {
        return "SELECT p.id_profesional, u.nombre, u.apellidos, u.email, u.telefono,
                       p.especialidad, p.disponible, p.duracion_media_consulta_min
                FROM profesional p
                INNER JOIN usuario u ON p.id_usuario = u.id_usuario";
    }

    public static function getProfesionalById(int $id): ?ProfesionalVo
    {
        $sql = self::baseSql() . " WHERE p.id_profesional = :id";

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

    public static function getProfesionalByIdUsuario(int $idUsuario): ?ProfesionalVo
    {
        $sql = self::baseSql() . " WHERE p.id_usuario = :id_usuario";

        try {
            $db   = self::getConnection();
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

    public static function getProfesionales(): ?array
    {
        $sql = self::baseSql() . " ORDER BY p.especialidad, u.apellidos";

        $profesionales = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $profesionales[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log('Error: ' . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($profesionales) ? $profesionales : null;
    }

    public static function crearProfesional(
        int $idUsuario,
        string $especialidad,
        string $numColegiado,
        int $duracion
    ): bool {
        $sql = "INSERT INTO profesional (id_usuario, especialidad, num_colegiado, duracion_media_consulta_min, disponible)
                VALUES (:id_usuario, :especialidad, :num_colegiado, :duracion, TRUE)";

        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':id_usuario',   $idUsuario,   PDO::PARAM_INT);
            $stmt->bindValue(':especialidad', $especialidad);
            $stmt->bindValue(':num_colegiado',$numColegiado);
            $stmt->bindValue(':duracion',     $duracion,    PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function actualizarDatosProfesional(
        int $idUsuario,
        string $especialidad,
        int $duracion,
        bool $disponible
    ): bool {
        $sql = "UPDATE profesional
                SET especialidad = :especialidad,
                    duracion_media_consulta_min = :duracion,
                    disponible = :disponible
                WHERE id_usuario = :id_usuario";

        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':especialidad', $especialidad);
            $stmt->bindValue(':duracion',     $duracion,             PDO::PARAM_INT);
            $stmt->bindValue(':disponible',   $disponible ? 1 : 0,  PDO::PARAM_INT);
            $stmt->bindValue(':id_usuario',   $idUsuario,            PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function buscarProfesionales(?string $busqueda): array
    {
        $sql = self::baseSql() . " WHERE p.disponible = 1";

        if ($busqueda !== null && $busqueda !== '') {
            $sql .= " AND (u.nombre    LIKE :q
                       OR  u.apellidos LIKE :q
                       OR  p.especialidad LIKE :q)";
        }

        $sql .= " ORDER BY p.especialidad ASC, u.apellidos ASC, u.nombre ASC";

        $profesionales = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            if ($busqueda !== null && $busqueda !== '') {
                $stmt->bindValue(':q', '%' . $busqueda . '%', PDO::PARAM_STR);
            }
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $profesionales[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log('Error: ' . $th->getMessage());
        } finally {
            $db = null;
        }

        return $profesionales;
    }

    private static function rowToVo(array $row): ProfesionalVo
    {
        return new ProfesionalVo(
            (int)  $row['id_profesional'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $row['telefono'],
            $row['especialidad'],
            (bool) $row['disponible'],
            isset($row['duracion_media_consulta_min']) ? (int)$row['duracion_media_consulta_min'] : null
        );
    }
}

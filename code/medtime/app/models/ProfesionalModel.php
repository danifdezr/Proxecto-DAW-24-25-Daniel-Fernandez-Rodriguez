<?php

namespace app\models;

use app\models\vo\ProfesionalVo;
use PDO;
use PDOException;

class ProfesionalModel extends Model
{
    public static function getProfesionalById(int $id): ?ProfesionalVo
    {
        $sql = "SELECT id_profesional, nombre, apellidos, email, telefono, especialidad, disponible 
                FROM profesional 
                WHERE id_profesional = :id";

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);

            $stmt->bindValue("id", $id, PDO::PARAM_INT);
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
        $sql = "SELECT id_profesional, nombre, apellidos, email, telefono, especialidad, disponible 
                FROM profesional";

        $profesionales = [];

        try {
            $db = self::getConnection();
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

    private static function rowToVo(array $row): ProfesionalVo
    {
        return new ProfesionalVo(
            $row['id_profesional'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $row['telefono'],
            $row['especialidad'],
            (bool) $row['disponible']
        );
    }
}
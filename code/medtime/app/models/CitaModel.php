<?php

namespace app\models;

use app\models\vo\CitaVo;
use PDO;
use PDOException;

class CitaModel extends Model
{
    public static function getCitaById(int $id): ?CitaVo
    {
        $sql = "SELECT id_cita, id_paciente, id_profesional, fecha_cita, hora_cita, estado, motivo, hora_estimada
                FROM cita
                WHERE id_cita = :id";

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

    public static function getCitas(): ?array
    {
        $sql = "SELECT id_cita, id_paciente, id_profesional, fecha_cita, hora_cita, estado, motivo, hora_estimada
                FROM cita";

        $citas = [];

        try {
            $db = self::getConnection();
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
            $row['id_cita'],
            $row['id_paciente'],
            $row['id_profesional'],
            $row['fecha_cita'],
            $row['hora_cita'],
            $row['estado'],
            $row['motivo'],
            $row['hora_estimada']
        );
    }
}
<?php

namespace app\models;

use app\models\vo\NotificacionVo;
use PDO;
use PDOException;

class NotificacionModel extends Model
{
    public static function getNotificacionById(int $id): ?NotificacionVo
    {
        $sql = "SELECT id_notificacion, id_cita, tipo, mensaje, fecha_envio, estado
                FROM notificacion
                WHERE id_notificacion = :id";

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

    public static function getNotificaciones(): ?array
    {
        $sql = "SELECT id_notificacion, id_cita, tipo, mensaje, fecha_envio, estado
                FROM notificacion";

        $notificaciones = [];

        try {
            $db = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $notificaciones[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($notificaciones) ? $notificaciones : null;
    }

    private static function rowToVo(array $row): NotificacionVo
    {
        return new NotificacionVo(
            (int) $row['id_notificacion'],
            (int) $row['id_cita'],
            $row['tipo'],
            $row['mensaje'],
            $row['fecha_envio'],
            $row['estado']
        );
    }
}

<?php

namespace app\models;

use app\models\vo\PacienteVo;
use PDO;
use PDOException;

class PacienteModel extends Model{

    public static function getPacienteById(int $id){
        $sql = "SELECT * FROM paciente WHERE id_paciente = :id";

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);

            $stmt->bindValue("id",$id, PDO::PARAM_INT);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $th) {
            error_log("Error". $th->getMessage());
        }finally{
            $db = null;
        }

        return isset($row) && $row ? self::rowToVo($row) : null;
    }

    public static function getPacientes(): ?array
    {
        $sql = "SELECT id_paciente, nombre, apellidos, email, telefono, dni, fecha_nacimiento
                FROM paciente";

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

    private static function rowToVo(array $row): PacienteVo
    {
        return new PacienteVo(
            $row['id_paciente'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $row['telefono'],
            $row['dni'],
            $row['fecha_nacimiento']
        );
    }
}
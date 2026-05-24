<?php

namespace app\models;

use app\models\vo\UsuarioVo;
use PDO;
use PDOException;

class UsuarioModel extends Model{

    public static function getUsuariosById(int $id){
        $sql = "SELECT * FROM usuario WHERE id_usuario = :id";

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

    public static function getUsers(): ?array {

        $sql = "SELECT id_usuario, nome, apelidos, email, telefono, contrasinal_hash, rol, data_alta, activo FROM usuario";
        $usuarios = [];

        try {
            $db = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $usuario = self::rowToVo($row);
                $usuarios[] = $usuario;
            }

        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($usuarios) ? $usuarios : null;
    }

    private static function rowToVo(array $row): UsuarioVo{

        $usuario = new UsuarioVo(
            $row['nome'],
            $row['apelidos'],
            $row['email'],
            $row['telefono'],
            $row['contrasinal_hash'],
            $row['rol'],
            $row['data_alta'],
            (bool) $row['activo']
        );

        return $usuario;
    }
}
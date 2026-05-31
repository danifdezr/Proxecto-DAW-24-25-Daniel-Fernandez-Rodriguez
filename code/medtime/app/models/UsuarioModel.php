<?php

namespace app\models;

use app\models\vo\UsuarioVo;
use PDO;
use PDOException;

class UsuarioModel extends Model
{
    public static function getUsuarioById(int $id): ?UsuarioVo
    {
        $sql = "SELECT id_usuario, nombre, apellidos, email, telefono, contrasena_hash, rol, fecha_alta, activo
                FROM usuario
                WHERE id_usuario = :id";

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

    public static function getUsuarioByEmail(string $email): ?UsuarioVo
    {
        $sql = "SELECT id_usuario, nombre, apellidos, email, telefono, contrasena_hash, rol, fecha_alta, activo
                FROM usuario
                WHERE email = :email";

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return isset($row) && $row ? self::rowToVo($row) : null;
    }

    public static function getUsuariosConProfesional(): array
    {
        $sql = "SELECT u.id_usuario, u.nombre, u.apellidos, u.email, u.telefono,
                       u.rol, u.activo, u.fecha_alta,
                       p.especialidad, p.disponible, p.duracion_media_consulta_min AS duracion_min
                FROM usuario u
                LEFT JOIN profesional p ON p.id_usuario = u.id_usuario
                ORDER BY u.id_usuario";

        $rows = [];

        try {
            $db   = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $rows;
    }

    public static function getUsuarios(): ?array
    {
        $sql = "SELECT id_usuario, nombre, apellidos, email, telefono, contrasena_hash, rol, fecha_alta, activo
                FROM usuario";

        $usuarios = [];

        try {
            $db = self::getConnection();
            $stmt = $db->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($rows as $row) {
                $usuarios[] = self::rowToVo($row);
            }
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return !empty($usuarios) ? $usuarios : null;
    }

    public static function actualizarPerfil(
        int $id,
        string $nombre,
        string $apellidos,
        string $email,
        string $telefono
    ): bool {
        $sql = "UPDATE usuario
                SET nombre = :nombre, apellidos = :apellidos,
                    email = :email, telefono = :telefono
                WHERE id_usuario = :id";

        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nombre',    $nombre);
            $stmt->bindValue(':apellidos', $apellidos);
            $stmt->bindValue(':email',     $email);
            $stmt->bindValue(':telefono',  $telefono);
            $stmt->bindValue(':id',        $id, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function cambiarContrasena(int $id, string $hash): bool
    {
        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare("UPDATE usuario SET contrasena_hash = :hash WHERE id_usuario = :id");
            $stmt->bindValue(':hash', $hash);
            $stmt->bindValue(':id',   $id, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function actualizarUsuario(
        int $id,
        string $nombre,
        string $apellidos,
        string $email,
        string $telefono,
        string $rol,
        bool $activo
    ): bool {
        $sql = "UPDATE usuario
                SET nombre = :nombre, apellidos = :apellidos, email = :email,
                    telefono = :telefono, rol = :rol, activo = :activo
                WHERE id_usuario = :id";

        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nombre',    $nombre);
            $stmt->bindValue(':apellidos', $apellidos);
            $stmt->bindValue(':email',     $email);
            $stmt->bindValue(':telefono',  $telefono);
            $stmt->bindValue(':rol',       $rol);
            $stmt->bindValue(':activo',    $activo ? 1 : 0, PDO::PARAM_INT);
            $stmt->bindValue(':id',        $id,             PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function eliminarUsuario(int $id): bool
    {
        $ok = false;

        try {
            $db   = self::getConnection();
            $stmt = $db->prepare("DELETE FROM usuario WHERE id_usuario = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $ok = $stmt->execute();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $ok;
    }

    public static function crearUsuario(
        string $nombre,
        string $apellidos,
        string $email,
        string $telefono,
        string $contrasenaHash,
        string $rol
    ): int {
        $sql = "INSERT INTO usuario (nombre, apellidos, email, telefono, contrasena_hash, rol)
                VALUES (:nombre, :apellidos, :email, :telefono, :contrasena_hash, :rol)";

        $id = 0;

        try {
            $db = self::getConnection();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':nombre', $nombre);
            $stmt->bindValue(':apellidos', $apellidos);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefono', $telefono);
            $stmt->bindValue(':contrasena_hash', $contrasenaHash);
            $stmt->bindValue(':rol', $rol);
            $stmt->execute();
            $id = (int) $db->lastInsertId();
        } catch (PDOException $th) {
            error_log("Error: " . $th->getMessage());
        } finally {
            $db = null;
        }

        return $id;
    }

    private static function rowToVo(array $row): UsuarioVo
    {
        return new UsuarioVo(
            (int) $row['id_usuario'],
            $row['nombre'],
            $row['apellidos'],
            $row['email'],
            $row['telefono'],
            $row['contrasena_hash'],
            $row['rol'],
            $row['fecha_alta'],
            (bool) $row['activo']
        );
    }
}

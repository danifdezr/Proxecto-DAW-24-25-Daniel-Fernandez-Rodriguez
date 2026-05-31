<?php

namespace app\models;

use PDO;
use PDOException;

class Model{
    protected static function getConnection(){
        try{
            $db = new PDO("mysql:host=mariadb;dbname=medtime;charset=utf8mb4", "medtime", "medtime");
        } catch (PDOException $th) {
            error_log("Error de conexión con la BD: " . $th->getMessage());
            die("Error de conexión con la base de datos. Por favor, inténtalo más tarde.");
        }
        return $db;
    }
}
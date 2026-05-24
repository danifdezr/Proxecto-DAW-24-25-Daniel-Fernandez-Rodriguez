<?php

namespace app\models;

use PDO;
use PDOException;

class Model{
    protected static function getConnection(){
        try{
            $db = new PDO("mysql:host=localhost;dbname=medtime;charset=utf8mb4", "daniel", "1234");
        }catch(PDOException $th){
            die("Error de conexión".$th->getMessage());
        }
        return $db;
    }
}
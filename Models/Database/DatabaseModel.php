<?php

namespace Database;

use PDO;

class DatabaseModel {

    private $conn;

    public function get_connection() {

        $this->conn = null;

        try
        {
            $this->conn = new PDO('mysql:host='.DB_HOST.';charset=utf8;dbname='.DB_NAME,DB_USER,DB_PASSWD);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            $this->conn->exec("set names utf8");
            return $this->conn;
        }
        catch (PDOException $e) {
            die($e->getMesage());
        }
    }
}

?>
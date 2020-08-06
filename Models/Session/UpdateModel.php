<?php

namespace Session;

use PDO;

class UpdateModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }
}
?>
<?php

namespace Session;

use PDO;

class ReadModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to read all session data based on user token
     */
    public function fetch_session_ontoken() {
        $stmt = "SELECT * FROM `$this->table_name`
        WHERE
        session_token = :sessiontoken";
        $query = $this->conn->prepare($stmt);
        $query->bindParam(':sessiontoken', $this->session_token);
        $query->execute();
        $num = $query->rowCount();
        if($num > 0) {
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->user_token = $row['user_token'];
                $this->session_token = $row['session_token'];
                $this->session_browser = $row['session_browser'];
                $this->session_os = $row['session_os'];
                $this->session_ip = $row['session_ip'];
                $this->created_on = $row['created_on'];
                $this->updated_on = $row['updated_on'];
                $this->expires_on = $row['expires_on'];
                return true;
            }
        }
    }
}
?>
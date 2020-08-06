<?php

namespace Session;

use PDO;

class DeleteModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to delete active session data based on device
     */
    public function delete_session_token() {
        $stmt = "DELETE FROM `$this->table_name`
        WHERE
        session_token = :sessiontoken";
        $query = $this->conn->prepare($stmt);
        $query->bindParam(':sessiontoken', $this->session_token);
        
        if($query->execute()) {
            return true;
        }
    }

    /**
     * function to delete all active session on all devices based on user token
     */
    public function delete_session_usertoken() {
        $stmt = "DELETE FROM `$this->table_name`
        WHERE
        user_token = :usertoken";
        $query = $this->conn->prepare($stmt);
        $query->bindParam(':usertoken', $this->user_token);

        if($query->execute()) {
            return true;
        }
    }

}
?>
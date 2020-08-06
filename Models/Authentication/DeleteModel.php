<?php

namespace Authentication;

use PDO;

/**
 * class container Delete functions performed on db.auth.table
 */

class DeleteModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to delete user account
     */
    public function delete_user_account() {
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
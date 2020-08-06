<?php

namespace Authentication;

use PDO;

/**
 * function contains ReadModel functions fetch data from Db.Auth.table
 */

class ReadModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to fetch user data based on user email
     */
    public function fetch_user_WithEmail() {
        $stmt = "SELECT * FROM `$this->table_name`
        WHERE
        user_email = :useremail";
        $query = $this->conn->prepare($stmt);
        $query->bindParam(':useremail', $this->user_email);
        $query->execute();
        $num = $query->rowCount();
        if($num > 0) {
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->user_email = $row['user_email'];
                $this->user_name = $row['user_name'];
                $this->user_password = $row['user_password'];
                $this->user_token = $row['user_token'];
                $this->user_profile_image = $row['user_profile_image'];
                $this->created_on = $row['created_on'];
                $this->updated_on = $row['updated_on'];
                return true;
            }
        }
    }

    /**
     * function to fetch user data based on user token
     */
    public function fetch_user_WithUserToken() {
        $stmt = "SELECT * FROM `$this->table_name`
        WHERE
        user_token = :usertoken";
        $query = $this->conn->prepare($stmt);
        $query->bindParam(':usertoken', $this->user_token);
        $query->execute();
        $num = $query->rowCount();
        if($num > 0) {
            while($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $this->user_email = $row['user_email'];
                $this->user_name = $row['user_name'];
                $this->user_password = $row['user_password'];
                $this->user_token = $row['user_token'];
                $this->user_profile_image = $row['user_profile_image'];
                $this->created_on = $row['created_on'];
                $this->updated_on = $row['updated_on'];
                return true;
            }
        }
    }

    /**
     * check if user email exists
     */
    public function user_email_exists() {
        $stmt = "SELECT user_email FROM `$this->table_name`
        WHERE
        user_email = :useremail";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':useremail', $this->user_email);
        $query->execute();
        $num = $query->rowCount();
        if($num > 0) {
            return true;
        }
    }

    /**
     * check if user name exists
     */
    public function user_name_exists() {
        $stmt = "SELECT user_name FROM `$this->table_name`
        WHERE
        user_name = :username";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':username', $this->user_name);
        $query->execute();
        $num = $query->rowCount();
        if($num > 0) {
            return true;
        }
    }
}
?>
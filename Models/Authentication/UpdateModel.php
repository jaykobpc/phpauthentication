<?php

namespace Authentication;

use PDO;

class UpdateModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to update user password;
     */

    public function update_password() {
        $stmt = "UPDATE `$this->table_name`
        SET
        user_password = :userpassword,
        updated_on = :updatedon
        WHERE
        user_token = :usertoken";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':userpassword', $this->user_password);
        $query->bindParam(':updatedon', $this->updated_on);
        $query->bindParam(':usertoken', $this->user_token);
        
        if($query->execute()) {
            return true;
        }
    }

    /**
     * function to update profile imagepath
     */
    public function update_image_path() {
        $stmt = "UPDATE `$this->table_name`
        SET
        user_profile_image = :userimagepath,
        updated_on = :updatedon
        WHERE
        user_token = :usertoken";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':userimagepath', $this->user_profile_image);
        $query->bindParam(':updatedon', $this->updated_on);
        $query->bindParam(':usertoken', $this->user_token);

        if($query->execute()) {
            return true;
        }
    }
}
?>
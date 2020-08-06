<?php

namespace Authentication;

use PDO;

/**
 * Class contains function to create new data to db
 * @Create.Model
 */

class CreateModel extends BaseModel {

    private $conn;

    /**
     * pass DB.Connection Instance -> onInit()
     */
    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to insert user data to Database.table
     */

    public function add_user_account() {
        $stmt = "INSERT INTO `$this->table_name`
        SET
        user_email = :useremail,
        user_name = :username,
        user_password = :userpassword,
        user_token = :usertoken,
        user_profile_image = :userprofileimage,
        created_on = :createdon,
        updated_on = :updatedon";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':useremail', $this->user_email);
        $query->bindParam(':username', $this->user_name);
        $query->bindParam(':userpassword', $this->user_password);
        $query->bindParam(':usertoken', $this->user_token);
        $query->bindParam(':userprofileimage', $this->user_profile_image);
        $query->bindParam(':createdon', $this->created_on);
        $query->bindParam(':updatedon', $this->updated_on);

        if($query->execute()) {
            return true;
        }
    }
}
?>
<?php

namespace Session;

use PDO;

class CreateModel extends BaseModel {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to add session
     */
    public function add_session() {
        $stmt = "INSERT INTO `$this->table_name`
        SET
        user_token = :usertoken,
        session_token = :sessiontoken,
        session_browser = :sessionbrowser,
        session_os = :sessionos,
        session_ip = :sessionip,
        created_on = :createdon,
        updated_on = :updatedon,
        expires_on = :expireson";

        $query = $this->conn->prepare($stmt);
        $query->bindParam(':usertoken', $this->user_token);
        $query->bindParam(':sessiontoken', $this->session_token);
        $query->bindParam(':sessionbrowser', $this->session_browser);
        $query->bindParam(':sessionos', $this->session_os);
        $query->bindParam(':sessionip', $this->session_ip);
        $query->bindParam(':createdon', $this->created_on);
        $query->bindParam(':updatedon', $this->updated_on);
        $query->bindParam(':expireson', $this->expires_on);

        if($query->execute()) {
            $this->write_session($this->user_token, $this->user_email, $this->session_token);
            return true;
        }
    }

    /**
     * function to write authentication session data
     */
    public function write_session($usertoken, $useremail, $sessiontoken) {
        if(!isset($_SESSION)) {
            session_start();
        }

        $_SESSION['sessiontoken'] = $sessiontoken;
    }
}
?>
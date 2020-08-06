<?php

namespace Database;

class DatabaseCreate {

    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    /**
     * function to execute all db create queries
     */
    public function create_all_tables() {
        $this->create_authentication_table();
        $this->create_sessions_table();
        return true;
    }

    /**
     *  function to create authentication table
     */
    protected function create_authentication_table() {
        $stmt = "CREATE TABLE IF NOT EXISTS ec_authentication(
            user_id INT(15) PRIMARY KEY AUTO_INCREMENT,
            user_email VARCHAR(60) NOT NULL,
            user_name VARCHAR(60) NOT NULL,
            user_password VARCHAR(200) NOT NULL,
            user_token VARCHAR(200) NOT NULL,
            user_profile_image TEXT NOT NULL,
            created_on TIMESTAMP(6) NOT NULL,
            updated_on TIMESTAMP(6) NOT NULL
        )";
        $query = $this->conn->prepare($stmt);
        $query->execute();
        return true;
    }

    /**
     * function to create sessions table
     */
    protected function create_sessions_table() {
        $stmt = "CREATE TABLE IF NOT EXISTS ec_session(
            session_id INT(15) PRIMARY KEY AUTO_INCREMENT,
            user_token VARCHAR(200) NOT NULL,
            session_token VARCHAR(200) NOT NULL,
            session_browser VARCHAR(150) NOT NULL,
            session_os VARCHAR(150) NOT NULL,
            session_ip VARCHAR(150) NOT NULL,
            created_on DATETIME(6) NOT NULL,
            updated_on TIMESTAMP(6) NOT NULL,
            expires_on TIMESTAMP(6) NOT NULL
        )";
        $query = $this->conn->prepare($stmt);
        $query->execute();
        return true;
    }
}
?>
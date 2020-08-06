<?php

namespace Authentication;

/**
 * Class contains Authentication variables
 */

class BaseModel {

    /**
     * Variables
     */
    protected $table_name = "ec_authentication";
    protected $user_id;
    protected $user_email;
    protected $user_name;
    protected $user_password;
    protected $user_token;
    protected $user_profile_image;
    protected $created_on;
    protected $updated_on;

    /**
     * custom setters
     */
    public function set_user_email($mail) {
        $this->user_email = $mail;
    }

    public function set_user_name($name) {
        $this->user_name = $name;
    }

    public function set_user_password($password) {
        $this->user_password = $password;
    }

    public function set_user_token($token) {
        $this->user_token = $token;
    }

    public function set_user_profile_image($path) {
        $this->user_profile_image = $path;
    }

    public function set_created_on($created) {
        $this->created_on = $created;
    }

    public function set_updated_on($updated) {
        $this->updated_on = $updated;
    }

    /**
     * custom getters
     */
    public function get_user_email() {
        return $this->user_email;
    }

    public function get_user_name() {
        return $this->user_name;
    }

    public function get_user_token() {
        return $this->user_token;
    }

    public function get_user_password() {
        return $this->user_password;
    }

    public function get_user_profile_image() {
        return $this->user_profile_image;
    }

    public function get_created_on() {
        return $this->created_on;
    }

    public function get_updated_on() {
        return $this->updated_on;
    }
}
?>
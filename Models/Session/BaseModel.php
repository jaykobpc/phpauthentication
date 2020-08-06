<?php

namespace Session;

class BaseModel {

    protected $table_name = "ec_session";
    protected $session_id;
    protected $user_token;
    protected $session_token;
    protected $session_browser;
    protected $session_os;
    protected $session_ip;
    protected $created_on;
    protected $updated_on;
    protected $expires_on;

    /**
     * custom setters
     */
    public function set_user_token($usrtoken) {
        $this->user_token = $usrtoken;
    }

    public function set_session_token($sesstoken) {
        $this->session_token = $sesstoken;
    }

    public function set_session_browser($browser) {
        $this->session_browser = $browser;
    }

    public function set_session_os($os) {
        $this->session_os = $os;
    }

    public function set_session_ip($ip) {
        $this->session_ip = $ip;
    }

    public function set_created_on($created) {
        $this->created_on = $created;
    }

    public function set_updated_on($updated) {
        $this->updated_on = $updated;
    }

    public function set_expires_on($expires) {
        $this->expires_on = $expires;
    }

    /**
     * custom getters
     */
    public function get_user_token() {
        return $this->user_token;
    }

    public function get_session_token() {
        return $this->session_token;
    }

    public function get_session_browser() {
        return $this->session_browser;
    }

    public function get_session_os() {
        return $this->session_os;
    }

    public function get_session_ip() {
        return $this->session_ip;
    }

    public function get_created_on() {
        return $this->created_on;
    }

    public function get_updated_on() {
        return $this->updated_on;
    }

    public function get_expires_on() {
        return $this->expires_on;
    }
}
?>
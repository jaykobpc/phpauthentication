<?php

namespace Utilities;

/**
 * Custom functions 
 */

class UtilitiesModel {

    protected $pref_str = "ABefPQURSghiCDvwxyz123EF67890#.GHIJKLMNOTUopqurVWXYZabcdjklmnstu45";

    //create sha 256
    public function create_sha256($v) {
        if($v === null || $v === "") {
            throw new Exception("TOKEN IS REQUIRED");
        }
        $str_rand = str_shuffle(substr($this->pref_str, 0, $v));
        $enc = hash('SHA256', $str_rand, false);
        return $enc;
    }

    //create sha 512
    public function create_sha512($v) {
        if($v === null || $v === "") {
            throw new Exception("TOKEN IS REQUIRED");
        }
        $str_rand = str_shuffle(substr($this->pref_str, 0, $v));
        $enc = hash('SHA512', $str_rand, false);
        return $enc;
    }

    //function to generate bcrypt password
    public function create_password($v) {
        if($v === null || $v === "") {
            throw new Exception("TOKEN IS REQUIRED");
        }
        $option = ['cost' => 12];
        $enc = password_hash($v, PASSWORD_BCRYPT, $option);
        return $enc;
    }
 
    //create base64
    public function create_base64($v) {
        if($v === null || $v === "") {
            throw new Exception("TOKEN IS REQUIRED");
        }
        $str_rand = str_shuffle(substr($this->pref_str, 0, $v));
        $enc = base64_encode($str_rand);
        return $enc;
    }

    //sanitize values
    public function sanitizeHtml($r) {
        $r = htmlspecialchars($r);
        $r = filter_var($r, FILTER_SANITIZE_STRING);
        return $r;
    }

    //random bcrypt token
    public function create_random_bcrypt($v) {
        if($v === null || $v === "") {
            throw new Exception("TOKEN IS REQUIRED");
        }
        $options = ['cost' => 13];
        $str_rand = str_shuffle(substr($this->pref_str, 0, $v));
        $enc = password_hash($str_rand, PASSWORD_BCRYPT, $options);
        return $enc;
    }

    //convert to json
    public function toJson($v) {
        return json_encode($v, JSON_PRETTY_PRINT);
    } 

    //get ip
    public function getIp() {
        $ip = '0.0.0.0'; //set default ip if client blocks ip check
        //
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
    
            $ip = $_SERVER['HTTP_CLIENT_IP'];
    
        } else if (!empty($_SERVER['HTTP_X_FORWARDED'])) {
    
            $ip = $_SERVER['HTTP_X_FORWARDED'];    
    
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    
        } else if (!empty($_SERVER['HTTP_FORWARDED'])) {
    
            $ip = $_SERVER['HTTP_FORWARDED'];
    
        } else {
            
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        return $ip;
    }
    
    //get browser name
    public function getUserAgent() {
        $ua = $_SERVER['HTTP_USER_AGENT'];
        return $ua;
    }

    //get date
    public function getDate() {
        $newDate = date('Y-m-d H:i:s');
        return $newDate;
    }

    //increment date
    public function getDateAdd($days) {
        $newdate = date('Y-m-d', strtotime("+{$days} days"));
        return $newdate;
    }

    //redirect
    public function redirect($page) {
        if($page == null || $page == '' || empty($page)) {
            return;
        }
        header("location:{$this->sanitizeHtml($page)}");
    }

    //input validator
    public function validateItem($string) {
        if(!empty($string) || $string !== null || $string !== '') {
            return $string;
        }
    } 

    //bind two values
    public function concat($a, $b) {
        if(!empty($a) || !empty($b)) {
            return $a . $b;
        }
    }

    //get operating system
    public function getOS($user_agent) { 
        $os_platform    =   "Unknown";
        $os_array = array(
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    
        foreach ($os_array as $regex => $value) { 
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }   
        return $os_platform;
    }

    //get user browser
    public function getBrowser($user_agent) {
        $browser = "Unknown";
        $browser_array = array(
            '/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/UCBrowser/i'  =>  'UC Browser',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
        );
    
        foreach ($browser_array as $regex => $value) { 
            if (preg_match($regex, $user_agent)) {
                $browser    =   $value;
            }
        }
        return $browser;
    }

    //get response codes
    public function server_response($response) {
        $response = "";
        switch($response) {
            case 'ok':
                return http_response_code(200);
                break;
            case 'created':
                return http_response_code(201);
                break;
            case 'no_content':
                return http_response_code(204);
                break;
            case 'bad_request':
                return http_response_code(400);
                break;
            case 'forbidden':
                return http_response_code(403);
                break;
            case 'unauthorized':
                return http_response_code(401);
                break;
            case 'request_timeout':
                return http_response_code(408);
                break;
            case 'not_modified':
                return http_response_code(304);
                break;
            case 'internal_server_error':
                return http_response_code(500);
                break;
            case 'bad_gateway':
                return http_response_code(502);
                break;
            case 'service_unavailable':
                return http_response_code(503);
                break;
            case 'method_not_allowed':
                return http_response_code(405);
                break;
            default:
                return http_response_code(200);
        } 
    }
}
?>
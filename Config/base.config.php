<?php
/**
 * @base configuration
 */

/**
 * SITE URL
 */
define('BASE_URL', 'http://localhost/AuthenticationPhp/');
/**
 * @Database HOST_NAME 
 */ 
define('DB_HOST', 'localhost'); 
/**
 * @Database NAME
 */
define('DB_NAME', 'phpauthapp');
/**
 * @Database USERNAME/UID
 */
define('DB_USER', 'root');
/**
 * @Database PASSWORD
 */
define('DB_PASSWD', '');
/**
 * Change to 'prod' => will hide system errors
 * Change to 'dev' => will display system errors
 */
define('environment', 'dev');
/**
 * SET YOUR TIME ZONE HERE
 */
date_default_timezone_set('Africa/Nairobi');
/**
 * Default Storage path for profiles
 */
define("STORAGE_PATH", "Storage/Profiles/");


/**
 * CAN BE Change with environment variable
 */
if(environment === 'prod') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(none);
}
else if (environment === 'dev')
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    error_reporting(-1);
}
?>
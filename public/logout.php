<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: ');
header('Access-Control-Max-Age: 3600');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Header, Authorization, X-Requested-With');

/**import all packages */
include($_SERVER['DOCUMENT_ROOT'] . '/' . 'AuthenticationPhp' . '/Models/main.sdk.php');

session_start();

/**require only used classes */
use Database\DatabaseModel as Database;
use Session\DeleteModel as SessionDelete;
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$sessdelete = new SessionDelete($conn);
$utilities = new Utilities();

/**delete current device session only */
if(isset($_SESSION['sessiontoken'])) {

    $get_session_token = isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : '';

    /**pass session token to session model for removal */
    $sessdelete->set_session_token($get_session_token);

    if($sessdelete->delete_session_token()) {
        /**remove session data */
        unset($_SESSION['sessiontoken']);
        unset($_SESSION['csrf_token']);
        unset($_SESSION['csrf_expiry']);
        
        session_destroy();
        session_unset();

        #redirect to login
        $utilities->redirect('login?exit=true');
    }
}
else {
    $utilities->redirect('login?active=false');
}
?>
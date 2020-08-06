<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET,POST');
header('Access-Control-Max-Age: 3600');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Header, Authorization, X-Requested-With');

/**import all packages */
include($_SERVER['DOCUMENT_ROOT'] . '/' . 'AuthenticationPhp' . '/Models/main.sdk.php');

session_start();

/**require only used classes */
use Database\DatabaseModel as Database;
use Authentication\CreateModel as AuthCreate;
use Authentication\ReadModel as AuthRead;
use Session\ReadModel as SessionRead;
use Session\DeleteModel as SessionDelete;
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$authcreate = new AuthCreate($conn);
$authread = new AuthRead($conn);
$_sessread = new SessionRead($conn);
$_sessdelete = new SessionDelete($conn);
$utilities = new Utilities();

/**messages state/store */
$msg_store = null;

/**check login state */
if(isset($_SESSION['sessiontoken'])) {

    /**get values for usertoken and session token */
    $session_token = isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : '';

    /**pass data to session reader */
    $_sessread->set_session_token($session_token);

    if($_sessread->fetch_session_ontoken()) {
        /**check if session is expired then validate */
        if($utilities->getDate() <= $_sessread->get_expires_on()) {
            if(
                $_sessread->get_session_token() != $session_token &&
                $_sessread->get_session_ip() != $utilities->getIp()
            ) {
                /** Delete session data */
                $_sessdelete->set_session_token($session_token);
                $_sessdelete->delete_session_token();
                
                /**redirect login page */
                $utilities->redirect('login?active=false');
            }
        }
    }
}
else {
    $utilities->redirect('login?active=false');
}


/**
 * Access the profile information
 */
if(isset($_SESSION['sessiontoken'])) {
     /**get values for usertoken and session token */
     $session_token = isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : '';

     /**pass data to session reader */
     $_sessread->set_session_token($session_token);

     if($_sessread->fetch_session_ontoken()) {

        /** get the session data */
        $authread->set_user_token($_sessread->get_user_token());

        /** fetch data with the user */
        if($authread->fetch_user_WithUserToken()) {

            $store = array();
            $store['logged_in'] = true;
            $store['token'] = $authread->get_user_token();
            $store['email'] = $authread->get_user_email();
            $store['username'] = $authread->get_user_name();
            $store['profile_image'] = BASE_URL . $authread->get_user_profile_image();

            if(!empty($store)) {
                echo $utilities->toJson($store);
            }
        }
    }
} 
else {
    $msg_store['error'] = true;
    $msg_store['message'] = "Request could not be handled from this end point";
}

if(!empty($msg_store)) {
    echo $utilities->toJson($msg_store);
}
?>
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: POST');
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
use Authentication\UpdateModel as AuthUpdate;
use Session\ReadModel as SessionRead;
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$authcreate = new AuthCreate($conn);
$authread = new AuthRead($conn);
$authupdate = new AuthUpdate($conn);
$_sessread = new SessionRead($conn);
$utilities = new Utilities();

/**messages state/store */
$msg_store = null;

/**
 * Access the profile information
 */
if($_SERVER["REQUEST_METHOD"] === "POST") {

    if(isset($_SESSION['sessiontoken'])) {
        /**get values for usertoken and session token */
        $session_token = isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : '';
   
        /**pass data to session reader */
        $_sessread->set_session_token($session_token);
   
        if($_sessread->fetch_session_ontoken()) {
   
           /** get the session data */
           $authread->set_user_token($_sessread->get_user_token());
   
           /** fetch data with the user token fetched */
           if($authread->fetch_user_WithUserToken()) {
   
               /**
                * update user profile image path based on the user token provided
                * move file to Storage/Profiles
                */
               if(isset($_FILES['image']['name'])) {
                $file_name =  isset($_FILES['image']) ? $_FILES['image']['name'] : null;
                $file_size = isset($_FILES['image']) ? $_FILES['image']['size'] : "";
                $file_tmp = isset($_FILES['image']) ? $_FILES['image']['tmp_name'] : "";
                $file_type = isset($_FILES['image']) ? $_FILES['image']['type'] : "";
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $extensions = array("jpeg","jpg","png");
                
                if(!in_array($file_ext, $extensions)){
                    $msg_store['error'] = true;
                    $msg_store['message'] = "This image format is not allowed";
                }
                else if($file_size > 5000000) {
                    $msg_store['error'] = true;
                    $msg_store['message'] = "File size cannot exceed 5(mb)";
                }
                else {
                    if($msg_store == null) {
                        //file data
                        $new_file_name = $utilities->concat($utilities->create_base64(12), "_" . $file_name);
                        //file path
                        $format_file_path = str_replace(" ", "_", $new_file_name);
                        $new_file_path = STORAGE_PATH . $new_file_name;
    
                        //upload data
                        $authupdate->set_user_token($authread->get_user_token());
                        $authupdate->set_updated_on($utilities->getDate());
                        $authupdate->set_user_profile_image($new_file_path);
    
                        $store = array();
    
                        //move image
                        $fnPath = $_SERVER['DOCUMENT_ROOT'] . "/" . "AuthenticationPhp/" . STORAGE_PATH . $new_file_name;
                        if(move_uploaded_file($file_tmp, $fnPath)) {
                            if($authupdate->update_image_path()) {
                                $store['error'] = false;
                                $store['message'] = "Image has been uploaded";
                            } else {
                                $store['error'] = true;
                                $store['message'] = "Could not upload image";
                            }
                        }
    
                        if(!empty($store)) {
                            echo $utilities->toJson($store);
                        }
                    }
                 }
               }
                else {
                   $msg_store['error'] = true;
                   $msg_store['message'] = 'Error';
                }
   
            }
        }
   }
}
else {
    $msg_store["error"] = true;
    $msg_store["message"] = "Request could not be handled from this end point";
}


if(!empty($msg_store)) {
    echo $utilities->toJson($msg_store);
}
?>
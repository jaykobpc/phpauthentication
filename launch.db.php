<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Max-Age: 3600');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Header, Authorization, X-Requested-With');

/**import all packages */
include($_SERVER['DOCUMENT_ROOT'] . '/' . basename(dirname(__FILE__)) . '/Models/main.sdk.php');

/**require only used classes */
use Database\DatabaseModel as Database;
use Database\DatabaseCreate as DatabaseCreate;
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$dbcreate = new DatabaseCreate($conn);
$utilities = new Utilities();

/**messages state/store */
$msg_store = [];

if($_SERVER['REQUEST_METHOD'] == 'GET') {
   try {
       if($dbcreate->create_all_tables()) {
           $msg_store['error'] = false;
           $msg_store['message'] = 'Database tables generated';
       } 
       else {
           $msg_store['error'] = true;
           $msg_store['message'] = 'Database tables could not be generated';
       }
   }
   catch (Exception $e) {
       $msg_store['error'] = true;
       $msg_store['message'] = $e->getMessage();
   }
}
else {
    $msg_store['error'] = true;
    $msg_store['message'] = 'Request could not be handled from this end point';
    $utilities->server_response('ok');
}

if(!empty($msg_store)) {
    echo $utilities->toJson($msg_store);
}
?>
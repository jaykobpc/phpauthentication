<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Methods: GET');
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
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$authcreate = new AuthCreate($conn);
$authread = new AuthRead($conn);
$_sessread = new SessionRead($conn);
$utilities = new Utilities();

/**messages state/store */
$msg_store = null;

$get_page_id = isset($_GET['gid']) ? $_GET['gid'] : '';

if($_SERVER['REQUEST_METHOD'] == 'GET') {
    if(!$utilities->validateItem($get_page_id)) {
        $utilities->redirect('login?exit=true');
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo BASE_URL . "Storage/System/app.build.css" ?>">
    <title>Authentication/success</title>
</head>

<body translate="no" class="no-select">
    <div class="utview" id="app">
        <div class="utcontainer">
            <img draggable="false" src="<?php echo BASE_URL . "Storage/System/success.png" ?>" alt="Success" class="utcontainer__img">
            <div class="utcontainer__textview">
                <h3 class="utcontainer__textview--lgtitle">Your account has been created</h3>
                <a style="text-decoration:none;" href="<?php echo BASE_URL . 'public/login'; ?>" class="utcontainer__textview--smtext">Click to login</a>
            </div>
        </div>
    </div>
</body>

</html>
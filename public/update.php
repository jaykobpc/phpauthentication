<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
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
use Session\DeleteModel as SessionDelete;
use Utilities\UtilitiesModel as Utilities;

/**instance of classes */
$database = new Database();
$conn = $database->get_connection();
$authcreate = new AuthCreate($conn);
$authread = new AuthRead($conn);
$authupdate = new AuthUpdate($conn);
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
 * variables
 */
$session_token = $utilities->sanitizeHtml(isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : "");
$oldPassword = $utilities->sanitizeHtml(isset($_POST['oldPassword']) ? $_POST['oldPassword'] : "");
$newPassword = $utilities->sanitizeHtml(isset($_POST['newPassword']) ? $_POST['newPassword'] : "");
$confirmPassword = $utilities->sanitizeHtml(isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : "");


if($_SERVER["REQUEST_METHOD"] === "POST") {
    if(!$utilities->validateItem($oldPassword)) {
        $msg_store = "Old password is required";
    }
    else if (!$utilities->validateItem($newPassword) || strlen($newPassword) < 5) {
        $msg_store = "New Password cannot be less than 5 characters";
    }
    else if (!$utilities->validateItem($confirmPassword)) {
        $msg_store = "Confirm password is required";
    }
    else if ($newPassword != $confirmPassword) {
        $msg_store = "Passwords do not match";
    }
    else {
        if($msg_store == null) {
            /**
             * get session token
             * get user token
             * get user password from user token
             * if passwords do not match reject 
             * else update
             */
            $_sessread->set_session_token($session_token);

            if($_sessread->fetch_session_ontoken()) {

                $authread->set_user_token($_sessread->get_user_token());

                if($authread->fetch_user_WithUserToken()) {
                    
                    //set password to BCRYPT
                    $converted_password = $utilities->create_password($oldPassword);

                    if(password_verify($oldPassword, $authread->get_user_password())) {

                        $authupdate->set_user_token($authread->get_user_token());
                        $authupdate->set_user_password($utilities->create_password($newPassword));
                        $authupdate->set_updated_on($utilities->getDate());

                        if($authupdate->update_password()) {
                            $msg_store = "Password has been updated";
                            header("location:home?enc=auto&success=true");
                        }
                    }
                    else {
                        $msg_store = "Current Password is not valid!";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo BASE_URL . "Storage/System/app.build.css" . '?=' . $utilities->create_base64(32);  ?>">
    <title>App/update</title>
</head>

<body translate="no" class="no-select">
<?php
     if($msg_store != null) {
        echo "
        <div class='msgbox text-center'>
            <div class='msgbox__text'>$msg_store</div>
        </div>
        ";
     }
    ?>
    <div class="fmcontainer">
        <div class="fmcontainer__form">
            <h3 class="fmcontainer__title">Reset</h3>
            <form accept-charset="utf-8" action="<?php echo $utilities->sanitizeHtml(BASE_URL . 'public/update'); ?>" method="POST" autocomplete="off">
                <div class="formgroup">
                    <label for="password" class="formgroup__label">Current password</label>
                    <input type="password" id="pwdInputA" name="oldPassword" placeholder="Current password"
                        class="formgroup__input" required>
                    <span id="pwdboxA" class="formgroup__iconview">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </span>
                </div>
                <div class="formgroup">
                    <label for="password" class="formgroup__label">New password</label>
                    <input type="password" id="pwdInputB" name="newPassword" placeholder="New password"
                        class="formgroup__input" required>
                    <span id="pwdboxB" class="formgroup__iconview">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </span>
                </div>
                <div class="formgroup">
                    <label for="password" class="formgroup__label">Confirm password</label>
                    <input type="password" id="pwdInputC" name="confirmPassword" placeholder="Confirm password"
                        class="formgroup__input" required>
                    <span id="pwdboxC" class="formgroup__iconview">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                        </svg>
                    </span>
                </div>
                <div class="formgroup">
                    <button type="submit" class="formgroup__button">Proceed</button>
                </div>
            </form>
            <div class="formgroup">
                <a href="<?php echo BASE_URL . "public/home" ?>" class="formgroup__gplink">Cancel</a>
            </div>
        </div>
    </div>

    <script>
        //IconButton
        var pwdboxA = document.getElementById("pwdboxA");
        var pwdboxB = document.getElementById("pwdboxB");
        var pwdboxC = document.getElementById("pwdboxC");
        //input box
        var pwdInputA = document.getElementById("pwdInputA");
        var pwdInputB = document.getElementById("pwdInputB");
        var pwdInputC = document.getElementById("pwdInputC");
        //icons
        var iconvisible = '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>';
        var iconNotVisible = '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>';

        pwdboxA.addEventListener("click", function () {
            pwdInputA.type == "password" ? pwdInputA.type = "text" : pwdInputA.type = "password";
            pwdInputA.type == "password" ? pwdboxA.innerHTML = iconvisible : pwdboxA.innerHTML = iconNotVisible;
        });

        pwdboxB.addEventListener("click", function () {
            pwdInputB.type == "password" ? pwdInputB.type = "text" : pwdInputB.type = "password";
            pwdInputB.type == "password" ? pwdboxB.innerHTML = iconvisible : pwdboxB.innerHTML = iconNotVisible;
        });

        pwdboxC.addEventListener("click", function () {
            pwdInputC.type == "password" ? pwdInputC.type = "text" : pwdInputC.type = "password";
            pwdInputC.type == "password" ? pwdboxC.innerHTML = iconvisible : pwdboxC.innerHTML = iconNotVisible;
        });
    </script>
</body>

</html>
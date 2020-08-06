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

/**check login state */
if(isset($_SESSION['usertoken']) || isset($_SESSION['sessiontoken'])) {
    /**get values for usertoken and session token */
    $user_token = isset($_SESSION['usertoken']) ? $_SESSION['usertoken'] : '';
    $session_token = isset($_SESSION['sessiontoken']) ? $_SESSION['sessiontoken'] : '';

    /**pass data to session reader */
    $_sessread->set_user_token($user_token);
    $_sessread->set_session_token($session_token);

    if($_sessread->fetch_session_ontoken()) {
        /**check if session is expired then validate */
        if($utilities->getDate() <= $_sessread->get_expires_on()) {
            if(
                $_sessread->get_user_token() == $user_token &&
                $_sessread->get_session_token() == $session_token &&
                $_sessread->get_session_ip() == $utilities->getIp()
            ) {
                /**redirect home page */
                $utilities->redirect('home?enc=utf8');
            }
        }
    }
}

/**csrf token utility */
$csrf_token = $utilities->sanitizeHtml(bin2hex(openssl_random_pseudo_bytes(32)));
$csrf_expiry = time() + 3600;

if(!isset($_SESSION['csrf_token']) && !isset($_SESSION['csrf_expiry'])) {
    $_SESSION['csrf_token'] = $csrf_token;
    $_SESSION['csrf_expiry'] = $csrf_expiry;
}

$get_csrf_token = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] :  '';
$get_csrf_time = isset($_SESSION['csrf_expiry']) ? $_SESSION['csrf_expiry'] : '';

/**get values */
$csrfusertoken = trim($utilities->sanitizeHtml(isset($_POST['_csrf_']) ? $_POST['_csrf_'] : ''));
$email = trim($utilities->sanitizeHtml(isset($_POST['email']) ? $_POST['email'] : ''));
$username = trim($utilities->sanitizeHtml(isset($_POST['username']) ? $_POST['username'] : ''));
$password = trim($utilities->sanitizeHtml(isset($_POST['password']) ? $_POST['password'] : ''));
$confirmPassword = trim($utilities->sanitizeHtml(isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : ''));

$profile_image = 'Storage/Profiles/default.png';

/**request handler */
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!$utilities->validateItem($email)) {
        $msg_store = "A valid email is required";
    }
    else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg_store = "A valid email is required";
    }
    else if (!$utilities->validateItem($username) || strlen($username) < 4) {
        $msg_store = "Username cannot be less than 4 characters";
    }
    else if (!$utilities->validateItem($password) || strlen($password) < 4) {
        $msg_store = "Password cannot be less than 4 characters";
    }
    else if (!$utilities->validateItem($confirmPassword)) {
        $msg_store = "Confirm password is required";
    }
    else if ($confirmPassword !== $password) {
        $msg_store = "Password do not match (confirm password)";
    }
    else {
        if(empty($msg_store) || $msg_store == null) {

            /**validate csrf token */
            if(hash_equals($_SESSION['csrf_token'], $csrfusertoken)) {
                if(time() >= $_SESSION['csrf_expiry']) {
                    $msg_store = "CSRF token has expired (reload)";
                    //remove any old csrf data
                    unset($_SESSION['csrf_expiry']);
                    unset($_SESSION['csrf_token']);
                    //reload page to add new data
                    header("Refresh:0");
                }
                else {
                    //pass data to create account model
                    $authcreate->set_user_email($email);
                    $authcreate->set_user_name($username);
                    $authcreate->set_user_token($utilities->create_sha256(32));
                    $authcreate->set_user_password($utilities->create_password($password));
                    $authcreate->set_user_profile_image($profile_image); //default profile image
                    $authcreate->set_created_on($utilities->getDate());
                    $authcreate->set_updated_on($utilities->getDate());

                    $authread->set_user_email($email);
                    $authread->set_user_name($username);

                    if($authread->user_email_exists()) {
                        $msg_store = "Email is already in use";
                    }
                    else if ($authread->user_name_exists()) {
                        $msg_store = "Username is already in use";
                    }
                    else {
                        /**create the account */
                        if($authcreate->add_user_account()) {
                            $email = $username = $password = null;
                            $utilities->redirect('success?gid='.$utilities->create_sha256(15).'');
                            /**get identification token */
                        }
                        else {
                            $msg_store = 'Account could not be created';
                            $email = $username = $password = null;
                        }
                    }
                }
            }
            else {
                $msg_store = "CSRF token has expired (reload)";
                //remove any old csrf data
                unset($_SESSION['csrf_expiry']);
                unset($_SESSION['csrf_token']);
                //reload page to add new data
                header("Refresh:0");
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
    <title>Authentication/signup</title>
</head>

<body translate="no" class="no-select">
    <div id="root">
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
            <form accept-charset="utf-8" action="<?php echo $utilities->sanitizeHtml(BASE_URL . 'public/signup'); ?>" method="POST" autocomplete="off">
                <div class="fmcontainer__form">
                    <h3 class="fmcontainer__title">Signup</h3>
                    <div class="formgroup">
                        <input type="hidden" name="_csrf_" value="<?php echo $utilities->sanitizeHtml($get_csrf_token); ?>">
                        <label for="email" class="formgroup__label">Email</label>
                        <input type="email" name="email" placeholder="Your email" class="formgroup__input" required>
                    </div>
                    <div class="formgroup">
                        <label for="username" class="formgroup__label">Username</label>
                        <input type="text" name="username" placeholder="Your username" class="formgroup__input" required>
                    </div>
                    <div class="formgroup">
                        <label for="password" class="formgroup__label">Password</label>
                        <input type="password" id="pwdInput" name="password" placeholder="Your password" class="formgroup__input"
                            required>
                        <span id="pwdboxA" class="formgroup__iconview">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" />
                            </svg>
                        </span>
                    </div>
                    <div class="formgroup">
                        <label for="password" class="formgroup__label">Confirm password</label>
                        <input type="password" id="pwdInputB" name="confirmPassword" placeholder="Confirm password"
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
                        <button type="submit" class="formgroup__button">Signup</button>
                    </div>
                    <div class="formgroup text-center">
                        <a href="<?php echo BASE_URL . 'public' . '/login'; ?>" class="formgroup__bottomlink">Already have an account?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        var pwdIconA = document.getElementById("pwdboxA");
        var pwdIconB = document.getElementById("pwdboxB");
        var pwdInputA = document.getElementById("pwdInput");
        var pwdInputB = document.getElementById("pwdInputB");

        var iconvisible = '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>';
        var iconNotVisible = '<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0zm0 0h24v24H0z" fill="none"/><path d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/></svg>';

        pwdIconA.addEventListener("click", function () {
            pwdInputA.type == "password" ? pwdInputA.type = "text" : pwdInputA.type = "password";
            pwdInputA.type == "password" ? pwdIconA.innerHTML = iconvisible : pwdIconA.innerHTML = iconNotVisible;
        });

        pwdIconB.addEventListener("click", function () {
            pwdInputB.type == "password" ? pwdInputB.type = "text" : pwdInputB.type = "password";
            pwdInputB.type == "password" ? pwdIconB.innerHTML = iconvisible : pwdIconB.innerHTML = iconNotVisible;
        });
    </script>
</body>

</html>
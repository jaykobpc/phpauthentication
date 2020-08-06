<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html; charset=UTF-8');
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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="stylesheet" href="<?php echo BASE_URL . "Storage/System/app.build.css" . '?=' . $utilities->create_base64(32);  ?>">
    <script src="<?php echo BASE_URL . "vendors/axios/dist/axios.min.js"; ?>"></script>
    <title>App/home</title>
</head>

<body translate="no" class="no-select">
    <div id="root">
        <div class="wxhomeview">
            <div class="wxcontainer">
                <div class="wxcontainer__imgbox">
                    <img id="imageview" draggable="false" src="" alt="Profile"
                        class="wxcontainer__imgbox--image">
                    <div title="change image" class="wxcontainer__imgbox--iconview">
                        <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                            <path d="M0 0h24v24H0z" fill="none" />
                            <circle cx="12" cy="12" r="3.2" />
                            <path
                                d="M9 2L7.17 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2h-3.17L15 2H9zm3 15c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5z" />
                        </svg>
                    </div>
                </div>
                <div class="wxcontainer__ftview">
                    <a href="<?php echo BASE_URL . "public/update"; ?>" class="wxcontainer__ftview--linktext">Change password</a>
                </div>
                <div class="wxcontainer__textview">
                    <div class="wxcontainer__textview--tab">
                        <h3 class="wxcontainer__textview--lgtext">Email :</h3>
                        <p id="emailtext" class="wxcontainer__textview--smtext">null</p>
                    </div>
                    <div class="wxcontainer__textview--tab">
                        <h3 class="wxcontainer__textview--lgtext">Username :</h3>
                        <p id="usernametext" class="wxcontainer__textview--smtext">null</p>
                    </div>
                </div>
                <div class="wxcontainer__footerview text-center">
                    <a href="<?php echo BASE_URL . "public/logout"; ?>" class="wxcontainer__footerview--btn">Logout</a>
                </div>
            </div>

            <div class="wxuploadmodal">
                <div class="wxuploadmodal__container">
                    <div class="wxuploadmodal__navbar">
                        <div title="close" class="wxuploadmodal__navbar--iconview">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                <path d="M0 0h24v24H0z" fill="none" />
                                <path d="M21 11H6.83l3.58-3.59L9 6l-6 6 6 6 1.41-1.41L6.83 13H21z" /></svg>
                        </div>
                        <h3 id="msgtext" class="wxuploadmodal__navbar--textview"></h3>
                    </div>
                    <div class="wxuploadmodal__widget">
                        <div id="uploadButton" class="wxuploadmodal__defaultview">
                            <input type="file" name="image" accept="image/*" hidden="hidden" id="imageviewselect">
                            <div class="wxuploadmodal__defaultview--icon">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24">
                                    <path d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM14 13v4h-4v-4H7l5-5 5 5h-3z" />
                                </svg>
                            </div>
                            <h3 class="wxuploadmodal__defaultview--text">Select or drag image here</h3>
                        </div>
                        <div class="wxuploadmodal__imageview display-none">
                            <img id="viewImage" draggable="false" src="<?php echo BASE_URL . "Storage/Profiles/default.png"; ?>"
                                class="wxuploadmodal__imageview--img">
                        </div>
                    </div>
                    <div class="wxuploadmodal__ftview">
                        <button id="uploadFileImage" class="wxuploadmodal__ftview--btn blue-fill">Upload</button>
                        <button id="removeImage" class="wxuploadmodal__ftview--btn red-fill">Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        var uploadModal = document.querySelector(".wxuploadmodal");
        var uploadModalButton = document.querySelector(".wxcontainer__imgbox--iconview");
        var uploadModalClose = document.querySelector(".wxuploadmodal__navbar--iconview");

        var email_text = document.getElementById("emailtext");
        var usernametext = document.getElementById("usernametext");
        var imageview = document.getElementById("imageview");

        var realFileButton = document.getElementById("imageviewselect");
        var uploadButton = document.getElementById("uploadButton");
        var viewImage = document.getElementById("viewImage");
        var imageViewContainer = document.querySelector(".wxuploadmodal__imageview");
        var removeImage = document.getElementById("removeImage");
        var uploadFileImage = document.getElementById("uploadFileImage");
        var msgtext = document.getElementById("msgtext");

        var formdata = new FormData();

        uploadModalButton.addEventListener("click", function (e) {
            e.preventDefault();
            uploadModal.classList.add("display-block");
        });

        uploadModalClose.addEventListener("click", function (e) {
            e.preventDefault();
            uploadModal.classList.remove("display-block");
            clearStage();
        });

        var imgtag = document.querySelectorAll("img");
        imgtag.forEach(function (imagetag) {
            imagetag.addEventListener("contextmenu", function (e) {
                e.preventDefault();
            });
        });

        uploadButton.addEventListener("click", function() {
            realFileButton.click();
        });

        realFileButton.addEventListener("change", function(e) {
            var file = e.target.files[0];

            if(file) {
                var reader = new FileReader();

                reader.addEventListener("load", function(e) {
                    uploadButton.classList.add("display-none");
                    uploadButton.style.display = "none";
                    imageViewContainer.classList.remove("display-none");
                    viewImage.setAttribute("src", e.target.result);
                });

                reader.readAsDataURL(file);
                formdata.append('image', file);

            } else {
                uploadButton.classList.remove("display-none");
                imageViewContainer.classList.add("display-none");
            }
        });

        function clearStage() {
            uploadButton.classList.remove("display-none");
            uploadButton.style.display = "block";
            imageViewContainer.classList.add("display-none");
            viewImage.setAttribute("src", "");
            msgtext.innerText = "";
        }

        removeImage.addEventListener("click", function() {
            clearStage();
        })

        function loadProfileData() {
            var docroot = document.location.origin;
            var fullPath = docroot + "/AuthenticationPhp/App/apis/profiledata";

            axios.get(fullPath)
                .then(function(response) {
                    //assign to profile
                    email_text.innerText = response.data.email;
                    usernametext.innerText = response.data.username;
                    imageview.setAttribute("src", response.data.profile_image);
                })
                .catch(function(err) {
                    console.log("Error: " + err);
                })
        }

        function uploadImage() {
            var docroot = document.location.origin;
            var fullPath = docroot + "/AuthenticationPhp/App/apis/uploadImage";

            axios.post(fullPath, formdata, { 
                header: { 
                    'Content-Type': 'multipart/form-data'
                } 
              })
              .then(function(response) {
                  msgtext.innerText = response.data.message;
                  loadProfileData();
              })
              .catch(function(err) {
                  console.log("Error: " + err);
              })

        }

        uploadFileImage.addEventListener("click", function() {
            uploadImage();
        });

        window.addEventListener("load", function() {
            loadProfileData();
        });
    </script>
</body>

</html>
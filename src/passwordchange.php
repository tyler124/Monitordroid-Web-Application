<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="https://www.monitordroid.com/app/favicon.ico">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <!--Bootstrap includes-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <!--end bootstrap-->
    <link rel="stylesheet" href="./main_style.css">
    <style>
    body {
        font-family: Helvetica, Arial, sans-serif;
        background: #525763;
        background: -moz-radial-gradient(center, ellipse cover, #525763 0%, #41464f 100%);
        background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,#525763), color-stop(100%,#41464f));
        background: -webkit-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: -o-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: -ms-radial-gradient(center, ellipse cover, #525763 0%,#41464f 100%);
        background: radial-gradient(ellipse at center, #525763 0%,#41464f 100%);
        filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#525763', endColorstr='#41464f',GradientType=1 );
        height: 500px;
    }
    .super-container {
        margin: 0;
        padding: 0;
        width: auto;
        height: auto;
        position: absolute;
        display: block;
        bottom: 0;
        top: 0;
        left: 0;
        right: 0;
        -webkit-box-shadow: inset 0 0 100px rgba(0,0,0,0.5);
        box-shadow: 0px 0px 100px rgba(0, 0, 0, 0.5) inset;
    }

    .password-change {
        display: inline-block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 15%;
        text-align: center;
    }
    .centered {
        text-align: center;
    }
    </style>
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <title>Change Password</title>
</head>
<body>
<div class="super-container">
    <div class="centered">
<?php
include_once './db_functions.php';
		require_once 'access.php';
		if (!userIsLoggedIn()) {
			include 'login.php';
			exit();
		}
$db = new DB_Functions();
if (isset($_POST['currentpwd']) and isset($_POST['newpass']) and isset ($_POST['newpasscnfm'])) {
	$currentpwd = $_POST['currentpwd'];
	$newpass = $_POST['newpass'];
	$newpasscnfm = $_POST['newpasscnfm'];
	if ($currentpwd == '' || $newpass== '' || $newpasscnfm== '') {
		?>
        <div class="password-change alert alert-danger fade in" role="alert">
            <p class="lead">
                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                <strong>Error</strong> Please fill out all fields.
                <br>
                <a class="alert-link" href="javascript:history.back()">Return and Fix</a>
            </p>
        </div>
        <?php
		exit();
	}
	if ($newpass != $newpasscnfm) {
		?>
        <div class="password-change alert alert-danger fade in" role="alert">
            <p class="lead">
                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                <strong>Error</strong> New password does not match confirmed password.
                <br>
                <a class="alert-link" href="javascript:history.back()">Return and Fix</a>
            </p>
        </div>
        <?php
		exit();
	}
	else
	if (checkPassword($currentpwd)) {
		$db = new DB_Functions();
		$hashedPassword = getPasswordHash($newpass);
		$db->changePassword($_SESSION['email'], $hashedPassword);
		?>
        <div class="password-change alert alert-success fade in" role="alert">
            <p class="lead">
                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>
                <strong>Success</strong> Your password has been changed!
            </p>
        </div>
        <script type="text/javascript">
        (function(){
            setTimeout(function(){window.location.href = "./"; }, 1200);
        })();
        </script>
        <?php
	}
	else {
		?>
        <div class="password-change alert alert-danger fade in" role="alert">
            <p class="lead">
                <span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>
                <strong>Error</strong> Current password entered is incorrect.
                <a class="alert-link" href="javascript:history.back()">Return and Fix</a>
            </p>
        </div>
        <?php
	}
}
function checkPassword($upassword)
{
	// returns whether unhashed upassword matches with hashed SESSION['password']
	require("./lib/PasswordHash.php");
	$hasher = new PasswordHash(8, false);
	// Just in case the hash isn't found
	$hashedPassword = "*";
	$hashedPassword = $_SESSION['password']; // I guess I can do this
	//$hashedPassword = $db->getPasswordByEmail($email);
	// Check that the password is correct, returns a boolean
	$check = $hasher->CheckPassword($upassword, $hashedPassword);
	return $check;
}
function getPasswordHash($upassword)
{
    if (strlen($upassword) > 72) { die("Password must be 72 characters or less"); }
    $db = new DB_Functions();
    require_once("./lib/PasswordHash.php");
    $hasher = new PasswordHash(8, false);
    $hashedPassword = $hasher->HashPassword($upassword);
    if (strlen($hashedPassword) < 20) {
      error_log("Critical error could not hash password", "accounts_log.log");
      exit();
    }
    return $hashedPassword;
}
?>
    </div>
</div>
</body>
</html>
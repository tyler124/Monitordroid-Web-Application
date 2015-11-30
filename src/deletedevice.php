<?php session_start(); ?>
<!DOCTYPE html>
<?php
	if(!isset($_SESSION['rowid'])){
	    header("location:index.php");
	    exit();
    }
	$rowid = $_SESSION["rowid"];

	include_once './db_functions.php';
	$db = new DB_Functions();
	//$db->deleteDevice($rowid);
    $email = $_SESSION['email'];
    $res = $db->deleteDeviceVerify($rowid, $email);
	unset($_SESSION["name"]);
	unset($_SESSION["rowid"]);
	unset($_SESSION["registration"]);
	?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="https://www.monitordroid.com/app2/favicon.ico">
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
    .logout {
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

    <title>Device Deleted</title>
    <script type="text/javascript">
    (function(){
        setTimeout(function(){window.history.back(); }, 1000);
    })();
    </script>
</head>
<body>
<div class="super-container">
    <div class="centered">
        <div class="logout alert alert-success fade in" role="alert">
            <p class="lead">
                <span class="glyphicon glyphicon-ok-sign" aria-hidden="true"></span>
                <strong>Success</strong> Device successfully deleted!
            </p>
        </div>
    </div>
</div>
</body>
</html>
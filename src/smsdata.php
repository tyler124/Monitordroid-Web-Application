<?php
if(!isset($_SESSION)) session_start();

    // receive data from HTML readsms request
    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

    $email = $_SESSION['email'];
    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    include_once './db_functions.php';

    $db = new DB_Functions();

    if (! $db->userDeviceVerify($rowId, $email)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit();
    }

    $updTime = 1;
    $updTime = $db->getSMSUpdateTime( $reg );

    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");

    $smslist = $db->getSMSInbox($reg);

    $mxr = explode("}", $smslist);
    $paragraphs;
    for ($i = 0; $i < count($mxr) - 1; $i += 1) {
        $paragraphs[$i] = json_decode(stripslashes($mxr[$i]) . '}', true);
    }

    if (count($paragraphs) == 0) {
        echo "[]";
    }
    else {
        echo json_encode($paragraphs);
    }
?>
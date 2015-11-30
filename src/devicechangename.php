<?php if(!isset($_SESSION)) session_start(); 
// devicechangename.php

	if ((!isset($_SESSION['email'])) || (!isset($_POST['id'])) || (!isset($_POST['new_name']))) {
	    header($_SERVER["SERVER_PROTOCOL"] . " 400 Bad Request");
	    exit();
    }
	
    $email = $_SESSION['email'];
    $rowId = $_POST['id'];
    
    $newName = $_POST['new_name'];


	include_once './db_functions.php';
	$db = new DB_Functions();
	$res = $db->changeDeviceName($rowId, $email, $newName);

    if (!$res){
        header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
        exit;
    } else {
        if (isset($_SESSION["rowid"]) && ($_SESSION["rowid"] == $rowId)) {
            $_SESSION["name"] = $newName;
        }
        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        exit;
    }
	?>
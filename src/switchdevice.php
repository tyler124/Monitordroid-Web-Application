<?php if (!isset($_SESSION)) { session_start(); }

if (!isset($_SESSION["email"]) || !isset($_SESSION["loggedIn"])) {
    header("location:index.php");
    exit();
}

include_once './db_functions.php';

$db = new DB_Functions();

if (! $db->userDeviceVerify($_POST["rowid"], $_SESSION["email"])) {
    header($_SERVER["SERVER_PROTOCOL"] . " 507 User Not Authorized for Device");
    exit();
}

$_SESSION["rowid"] = $_POST["rowid"];
$_SESSION["registration"] = $_POST["registration"];
$_SESSION["name"] = $_POST["name"];


?>
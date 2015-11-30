<?php session_start();
/* Clear locationlog.php */
	if(!isset($_SESSION['registration']) || !isset($_SESSION['rowid']) || !isset($_POST['regId']) || ($_POST['regId'] != $_SESSION['registration'])){
	    header("location:index.php");
	    exit();
    }
	$regid = $_POST["regId"];

	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->clearLocationLogs($regid);

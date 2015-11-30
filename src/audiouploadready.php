<?php
    if(!isset($_SESSION)) session_start();

    // receive data from HTML audiouploadready request
    if (!isset($_SESSION['email']) || !isset($_POST['rowid']) ||
      !isset($_POST['registration']) || !isset($_POST['reqtime']) ||
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

	$currentDir = getcwd();
	$fileloc = $currentDir . '/files/sound.m4a';

    $reg = $_POST["registration"];
    $rowId = $_POST["rowid"];
    $reqTime = intval($_POST["reqtime"]);

    $updTime = 1;
    $updTime = filemtime($fileloc);


    error_log('filemtime: ' . $updTime);
    error_log('reqtime: ' . $reqTime);

    if ($reqTime > $updTime) {
        header($_SERVER["SERVER_PROTOCOL"] . " 204 No Content");
        exit();
    }
    setcookie("audio_filetime", $updTime);
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
?>
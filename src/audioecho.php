<?php 
if(!isset($_SESSION)) session_start();

    if (!isset($_SESSION['email']) || 
      !isset($_SESSION['loggedIn'])) {
        header("location:index.php");
        exit();     
    }

	$currentDir = getcwd();
	$fileloc = $currentDir . '/files/sound.m4a';

if (!isset($_GET["d"]) || $_GET["d"] != "download") {
    header('Content-Disposition: inline;filename="user_sound.m4a"');
} else {
    header('Content-Disposition: attachment;filename="user_sound.m4a"');
}
    
    header("Content-Transfer-Encoding: binary"); 
    header("Content-Type: audio/mp4");
    header('X-Pad: avoid browser bug');
    header('Cache-Control: no-cache');

    echo file_get_contents($fileloc);
	
?>
<?php 
if(!isset($_SESSION)) session_start();

if (!isset($_SESSION["email"])) {
    header("location:login.php");
    exit();
}


$email = $_SESSION["email"];

$currentDir = getcwd();
$fileloc = $currentDir . '/files/picture.jpg';

header('Content-Disposition: attachment; filename="' . $picture . '"');
header("Content-Transfer-Encoding: binary"); 

$ext = strtolower(end(explode(".", $picture)));

header("Content-Type: image/jpeg");
header('X-Pad: avoid browser bug');
header('Cache-Control: no-cache');


echo file_get_contents($fileloc);
?>
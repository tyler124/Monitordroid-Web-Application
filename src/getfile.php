<?php 
if(!isset($_SESSION)) session_start();

if (!isset($_SESSION["email"])) {
    header("location:login.php");
    exit();
}

$ext = $_GET["ext"];
$currentDir = getcwd();
$fileloc = $currentDir . '/files/uploadedfile' . $ext;

if (isset($_GET["name"])) {
    header('Content-Disposition: attachment; filename="' . $_GET["name"] . '"');
}
else {
    header('Content-Disposition: attachment; filename="uploadedfile.' . $ext . '"');
}
header("Content-Transfer-Encoding: binary"); 

$ext = strtolower(end(explode(".", $picture)));

if ($ext == "jpg" || $ext == "jpeg" || $ext == "png" || $ext == "gif") {
    header("Content-Type: image/" . $ext);
}
else {
    header("Content-Type: application/octet-stream");
}
header('X-Pad: avoid browser bug');
header('Cache-Control: no-cache');


echo file_get_contents($fileloc);
?>
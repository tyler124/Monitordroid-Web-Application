<?php 
if(!isset($_SESSION)) session_start();

if (!isset($_SESSION["email"])) {
	header("location:index.php");
	exit();
}

$start_time = $_POST["start_time"];

$currentDir = getcwd();
$fileloc = $currentDir . '/files/' . 'picture.jpg';
$spxr = filemtime($fileloc);
$date1 = $spxr;
$date2 = $start_time;
 
//Calculate the difference.
$difference = $date1 - $date2;


if ($difference > 0) {
	echo "true";
}
else {
	echo "false";
}
?>
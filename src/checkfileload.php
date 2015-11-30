<?php 
if(!isset($_SESSION)) session_start();

if (!isset($_SESSION["email"])) {
	header("location:index.php");
	exit();
}

$start_time = $_POST["start_time"];
$ext = $_POST["ext"];
$currentDir = getcwd();
$filepath = $currentDir . '/files/'. "uploadedfile." . $ext;
$spxr = filemtime($filepath);

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
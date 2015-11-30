<?php
   // receive data from app's http request
    $pictureDir = $_POST["pictureDirectory"];
	$rName=$_POST["regName"];

	include_once './db_functions.php';
	$db = new DB_Functions();
	$db->insertPictureDir($rName, $pictureDir);
    ?>